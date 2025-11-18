<?php

namespace App\Livewire\BackupRestore;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\BackupDestination\Backup;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\BackupSchedule;

class BackupRestoreIndex extends Component
{
    public $backups = [];
    public $backupInProgress = false;
    public $restoreInProgress = false;
    public $selectedBackup = null;
    public $message = '';
    public $messageType = ''; // success, error, info

    // Auto Backup Properties
    public $backupSchedules = [];
    public $showScheduleModal = false;
    public $showRestorePasswordModal = false;
    public $editingSchedule = null;
    public $restorePassword = '';
    public $scheduleForm = [
        'name' => '',
        'frequency' => 'daily',
        'time' => '02:00',
        'day_of_week' => 1,
        'day_of_month' => 1,
        'is_active' => true,
        'description' => '',
        'encryption_enabled' => false,
        'encryption_password' => ''
    ];

    public function mount()
    {
        $this->loadBackups();
        $this->loadBackupSchedules();
    }

    public function loadBackups()
    {
        $backupDestination = BackupDestination::create('local', env('APP_NAME', 'Laravel'));

        $this->backups = $backupDestination->backups()
            ->filter(function (Backup $backup) {
                return $backup->exists();
            })
            ->map(function (Backup $backup) {
                return [
                    'path' => $backup->path(),
                    'date' => $backup->date(),
                    'size' => $backup->sizeInBytes(),
                    'size_human' => $this->formatBytes($backup->sizeInBytes()),
                    'name' => basename($backup->path()),
                ];
            })
            ->sortByDesc('date')
            ->values()
            ->toArray();
    }

    public function loadBackupSchedules()
    {
        $this->backupSchedules = BackupSchedule::orderBy('created_at', 'desc')->get();
    }

    public function openScheduleModal($scheduleId = null)
    {
        if ($scheduleId) {
            $schedule = BackupSchedule::find($scheduleId);
            if ($schedule) {
                $this->editingSchedule = $schedule;
                $this->scheduleForm = [
                    'name' => $schedule->name,
                    'frequency' => $schedule->frequency,
                    'time' => $schedule->time ? $schedule->time->format('H:i') : '02:00',
                    'day_of_week' => $schedule->day_of_week ?? 1,
                    'day_of_month' => $schedule->day_of_month ?? 1,
                    'is_active' => $schedule->is_active,
                    'description' => $schedule->description ?? '',
                    'encryption_enabled' => $schedule->encryption_enabled ?? false,
                    'encryption_password' => '' // Don't load existing password for security
                ];
            }
        } else {
            $this->resetScheduleForm();
        }

        $this->showScheduleModal = true;
    }

    public function closeScheduleModal()
    {
        $this->showScheduleModal = false;
        $this->editingSchedule = null;
        $this->resetScheduleForm();
    }

    private function resetScheduleForm()
    {
        $this->scheduleForm = [
            'name' => '',
            'frequency' => 'daily',
            'time' => '02:00',
            'day_of_week' => 1,
            'day_of_month' => 1,
            'is_active' => true,
            'description' => '',
            'encryption_enabled' => false,
            'encryption_password' => ''
        ];
    }

    public function saveSchedule()
    {
        if (!auth()->user()->can('backup-restore.create')) {
            $this->message = 'Anda tidak memiliki izin untuk mengelola backup schedule.';
            $this->messageType = 'error';
            return;
        }

        $this->validate([
            'scheduleForm.name' => 'required|string|max:255',
            'scheduleForm.frequency' => 'required|in:daily,weekly,monthly',
            'scheduleForm.time' => 'required|date_format:H:i',
            'scheduleForm.day_of_week' => 'nullable|integer|min:0|max:6',
            'scheduleForm.day_of_month' => 'nullable|integer|min:1|max:28',
            'scheduleForm.is_active' => 'boolean',
            'scheduleForm.description' => 'nullable|string|max:1000',
            'scheduleForm.encryption_enabled' => 'boolean',
            'scheduleForm.encryption_password' => 'nullable|string|min:8|max:255'
        ]);

        // Validate password if encryption is enabled
        if ($this->scheduleForm['encryption_enabled'] && empty($this->scheduleForm['encryption_password'])) {
            $this->addError('scheduleForm.encryption_password', 'Password enkripsi wajib diisi jika enkripsi diaktifkan.');
            return;
        }

        try {
            $data = [
                'name' => $this->scheduleForm['name'],
                'frequency' => $this->scheduleForm['frequency'],
                'time' => $this->scheduleForm['time'],
                'is_active' => $this->scheduleForm['is_active'],
                'description' => $this->scheduleForm['description'],
                'encryption_enabled' => $this->scheduleForm['encryption_enabled'],
                'encryption_password' => $this->scheduleForm['encryption_enabled'] ? $this->scheduleForm['encryption_password'] : null
            ];

            // Add frequency-specific fields
            if ($this->scheduleForm['frequency'] === 'weekly') {
                $data['day_of_week'] = $this->scheduleForm['day_of_week'];
            } elseif ($this->scheduleForm['frequency'] === 'monthly') {
                $data['day_of_month'] = $this->scheduleForm['day_of_month'];
            }

            if ($this->editingSchedule) {
                $this->editingSchedule->update($data);
                $this->message = 'Backup schedule berhasil diperbarui!';
            } else {
                BackupSchedule::create($data);
                $this->message = 'Backup schedule berhasil dibuat!';
            }

            $this->messageType = 'success';
            $this->closeScheduleModal();
            $this->loadBackupSchedules();

        } catch (\Exception $e) {
            $this->message = 'Gagal menyimpan backup schedule: ' . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function deleteSchedule($scheduleId)
    {
        if (!auth()->user()->can('backup-restore.delete')) {
            $this->message = 'Anda tidak memiliki izin untuk menghapus backup schedule.';
            $this->messageType = 'error';
            return;
        }

        try {
            $schedule = BackupSchedule::find($scheduleId);
            if ($schedule) {
                $schedule->delete();
                $this->message = 'Backup schedule berhasil dihapus!';
                $this->messageType = 'success';
                $this->loadBackupSchedules();
            }
        } catch (\Exception $e) {
            $this->message = 'Gagal menghapus backup schedule: ' . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function toggleScheduleStatus($scheduleId)
    {
        if (!auth()->user()->can('backup-restore.create')) {
            $this->message = 'Anda tidak memiliki izin untuk mengubah status backup schedule.';
            $this->messageType = 'error';
            return;
        }

        try {
            $schedule = BackupSchedule::find($scheduleId);
            if ($schedule) {
                $schedule->update(['is_active' => !$schedule->is_active]);
                $this->loadBackupSchedules();
                $this->message = 'Status backup schedule berhasil diubah!';
                $this->messageType = 'success';
            }
        } catch (\Exception $e) {
            $this->message = 'Gagal mengubah status backup schedule: ' . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function runScheduleNow($scheduleId)
    {
        if (!auth()->user()->can('backup-restore.create')) {
            $this->message = 'Anda tidak memiliki izin untuk menjalankan backup schedule.';
            $this->messageType = 'error';
            return;
        }

        try {
            Artisan::call('backup:run-auto', ['--schedule' => $scheduleId]);
            $this->message = 'Backup schedule berhasil dijalankan!';
            $this->messageType = 'success';
            $this->loadBackupSchedules();
        } catch (\Exception $e) {
            $this->message = 'Gagal menjalankan backup schedule: ' . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function createBackup()
    {
        $this->backupInProgress = true;
        $this->message = '';
        $this->messageType = '';

        try {
            // Run the backup command
            Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true,
            ]);

            $output = Artisan::output();

            if (str_contains($output, 'Backup completed!')) {
                $this->message = 'Backup berhasil dibuat!';
                $this->messageType = 'success';
                $this->loadBackups();
            } else {
                $this->message = 'Terjadi kesalahan saat membuat backup: ' . $output;
                $this->messageType = 'error';
            }
        } catch (\Exception $e) {
            $this->message = 'Gagal membuat backup: ' . $e->getMessage();
            $this->messageType = 'error';
        } finally {
            $this->backupInProgress = false;
        }
    }

    public function downloadBackup($backupName)
    {
        if (!auth()->user()->can('backup-restore.download')) {
            $this->message = 'Anda tidak memiliki izin untuk mendownload backup.';
            $this->messageType = 'error';
            return;
        }

        $backupPath = storage_path('app/private/' . env('APP_NAME', 'Laravel') . '/' . $backupName);

        if (!file_exists($backupPath)) {
            $this->message = 'File backup tidak ditemukan.';
            $this->messageType = 'error';
            return;
        }

        return response()->download($backupPath);
    }

    /**
     * Check if backup file is encrypted
     */
    private function isBackupEncrypted(string $backupPath): bool
    {
        try {
            $zip = new \ZipArchive();
            $result = $zip->open($backupPath);

            if ($result !== true) {
                return false; // Not a valid zip file
            }

            $fileCount = $zip->numFiles;
            if ($fileCount === 0) {
                $zip->close();
                return false;
            }

            $firstFile = $zip->getNameIndex(0);

            // Test extraction without password
            $tempDir = sys_get_temp_dir() . '/backup_encryption_test_' . time();
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $extractWithoutPassword = $zip->extractTo($tempDir, $firstFile);

            if ($extractWithoutPassword) {
                // Successfully extracted without password - not encrypted
                $zip->close();

                // Cleanup
                if (is_dir($tempDir)) {
                    $this->deleteDirectory($tempDir);
                }

                return false;
            } else {
                // Failed to extract without password - try with test password
                // Note: We can't actually test with real password here as we don't know it
                // This method assumes that if extraction fails, it's encrypted
                $zip->close();

                // Cleanup temp directory
                if (is_dir($tempDir)) {
                    $this->deleteDirectory($tempDir);
                }

                return true; // Assume encrypted if extraction fails
            }

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Recursively delete directory
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    public function confirmRestore($backupName)
    {
        $this->selectedBackup = $backupName;

        $backupPath = storage_path('app/private/' . env('APP_NAME', 'Laravel') . '/' . $backupName);

        if (!file_exists($backupPath)) {
            $this->message = 'File backup tidak ditemukan.';
            $this->messageType = 'error';
            return;
        }

        // Check if backup is encrypted
        if ($this->isBackupEncrypted($backupPath)) {
            $this->showRestorePasswordModal = true;
            $this->restorePassword = '';
            $this->dispatch('open-restore-password-modal');
        } else {
            $this->dispatch('open-restore-modal');
        }
    }

    public function restoreBackup($password = null)
    {
        if (!auth()->user()->can('backup-restore.restore')) {
            $this->message = 'Anda tidak memiliki izin untuk merestore database.';
            $this->messageType = 'error';
            return;
        }

        if (!$this->selectedBackup) {
            $this->message = 'Tidak ada backup yang dipilih.';
            $this->messageType = 'error';
            return;
        }

        $this->restoreInProgress = true;
        $this->message = '';
        $this->messageType = '';

        try {
            $backupPath = storage_path('app/private/' . env('APP_NAME', 'Laravel') . '/' . $this->selectedBackup);

            if (!file_exists($backupPath)) {
                throw new \Exception('File backup tidak ditemukan.');
            }

            $dbConnection = config('database.default');
            $dbConfig = config('database.connections.' . $dbConnection);

            // Check if backup is encrypted
            $isEncrypted = $this->isBackupEncrypted($backupPath);
            $encryptionPassword = $password ?: $this->restorePassword;

            if ($dbConnection === 'sqlite') {
                // Handle SQLite restore
                $dbPath = database_path('database.sqlite');
                $backupDbPath = $dbPath . '.backup';

                // Create a backup of current database before restore
                if (file_exists($dbPath)) {
                    File::copy($dbPath, $backupDbPath);
                }

                // Extract and restore from zip file
                $zip = new \ZipArchive();
                if ($zip->open($backupPath) === TRUE) {
                    // Find the database file in the backup
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if (str_contains($filename, 'database.sqlite')) {
                            $zip->extractTo(sys_get_temp_dir(), $filename);
                            $extractedDbPath = sys_get_temp_dir() . '/' . $filename;

                            // Copy the extracted database to replace current one
                            File::copy($extractedDbPath, $dbPath);

                            // Clean up extracted file
                            File::delete($extractedDbPath);
                            break;
                        }
                    }
                    $zip->close();
                } else {
                    throw new \Exception('Gagal membuka file backup.');
                }
            } elseif ($dbConnection === 'mysql') {
                // Handle MySQL restore
                $zip = new \ZipArchive();

                // Set password if encrypted
                if ($isEncrypted && $encryptionPassword) {
                    $zip->setPassword($encryptionPassword);
                }

                if ($zip->open($backupPath) === TRUE) {
                    // Find the SQL dump file in the backup
                    $sqlFile = null;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if (str_ends_with($filename, '.sql')) {
                            $sqlFile = $filename;
                            break;
                        }
                    }

                    if (!$sqlFile) {
                        throw new \Exception('File SQL dump tidak ditemukan dalam backup.');
                    }

                    // Extract SQL file to temp directory
                    $tempDir = sys_get_temp_dir() . '/backup_restore_' . time();
                    if (!is_dir($tempDir)) {
                        mkdir($tempDir, 0755, true);
                    }

                    $zip->extractTo($tempDir, $sqlFile);
                    $zip->close();

                    $sqlFilePath = $tempDir . '/' . $sqlFile;

                    // Restore MySQL database using mysql command
                    $command = sprintf(
                        'mysql --host=%s --port=%s --user=%s --password=%s %s < "%s"',
                        escapeshellarg($dbConfig['host']),
                        escapeshellarg($dbConfig['port']),
                        escapeshellarg($dbConfig['username']),
                        escapeshellarg($dbConfig['password']),
                        escapeshellarg($dbConfig['database']),
                        $sqlFilePath
                    );

                    $output = [];
                    $returnCode = 0;
                    exec($command, $output, $returnCode);

                    // Clean up temp files
                    File::delete($sqlFilePath);
                    rmdir($tempDir);

                    if ($returnCode !== 0) {
                        throw new \Exception('Gagal mengimport database MySQL. Return code: ' . $returnCode);
                    }
                } else {
                    throw new \Exception('Gagal membuka file backup.');
                }
            } else {
                throw new \Exception('Tipe database tidak didukung untuk restore.');
            }

            $this->message = 'Database berhasil direstore!';
            $this->messageType = 'success';
            $this->selectedBackup = null;

        } catch (\Exception $e) {
            $this->message = 'Gagal restore database: ' . $e->getMessage();
            $this->messageType = 'error';
        } finally {
            $this->restoreInProgress = false;
        }
    }

    public function deleteBackup($backupName)
    {
        if (!auth()->user()->can('backup-restore.delete')) {
            $this->message = 'Anda tidak memiliki izin untuk menghapus backup.';
            $this->messageType = 'error';
            return;
        }

        try {
            $backupPath = storage_path('app/private/' . env('APP_NAME', 'Laravel') . '/' . $backupName);

            if (file_exists($backupPath)) {
                File::delete($backupPath);
                $this->message = 'Backup berhasil dihapus!';
                $this->messageType = 'success';
                $this->loadBackups();
            } else {
                $this->message = 'File backup tidak ditemukan.';
                $this->messageType = 'error';
            }
        } catch (\Exception $e) {
            $this->message = 'Gagal menghapus backup: ' . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function submitRestorePassword()
    {
        $this->validate([
            'restorePassword' => 'required|string|min:1'
        ]);

        $this->closeRestorePasswordModal();
        $this->restoreBackup($this->restorePassword);
    }

    public function closeRestorePasswordModal()
    {
        $this->showRestorePasswordModal = false;
        $this->restorePassword = '';
    }

    public function cancelRestore()
    {
        $this->selectedBackup = null;
    }

    private function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }

    public function render()
    {
        return view('livewire.backup-restore.backup-restore-index');
    }
}
