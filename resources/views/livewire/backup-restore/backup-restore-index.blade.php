<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Backup & Restore Database') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Kelola backup dan restore database aplikasi') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Messages -->
    @if($message)
        <x-alert type="{{ $messageType }}" class="mb-4">
            {{ $message }}
        </x-alert>
    @endif

    <!-- Create Backup Section -->
    <div class="mb-8">
        <flux:heading size="md" level="2" class="mb-4">{{ __('Buat Backup Baru') }}</flux:heading>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="sm" level="3">{{ __('Backup Database') }}</flux:heading>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Buat backup database saat ini untuk keamanan data
                    </p>
                </div>
                @if($backupInProgress)
                    <flux:button
                        variant="primary"
                        wire:loading.attr="disabled"
                        disabled
                        icon="arrow-path">
                        Membuat Backup...
                    </flux:button>
                @else
                    @can('backup-restore.create')
                        <flux:button
                            variant="primary"
                            wire:click="createBackup"
                            icon="archive-box">
                            Buat Backup
                        </flux:button>
                    @endcan
                @endif
            </div>
        </div>
    </div>

    <!-- Auto Backup Section -->
    <div class="mb-8">
        <flux:heading size="md" level="2" class="mb-4">{{ __('Auto Backup Schedule') }}</flux:heading>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <flux:heading size="sm" level="3">{{ __('Jadwal Backup Otomatis') }}</flux:heading>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Atur backup database yang berjalan secara otomatis sesuai jadwal
                    </p>
                </div>
                @can('backup-restore.create')
                    <flux:button
                        variant="primary"
                        wire:click="openScheduleModal"
                        icon="calendar-days">
                        Tambah Jadwal
                    </flux:button>
                @endcan
            </div>

            @if(count($backupSchedules) > 0)
                <div class="space-y-4">
                    @foreach($backupSchedules as $schedule)
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <flux:icon.calendar-days class="h-5 w-5 text-gray-400" />
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $schedule->name }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ ucfirst($schedule->frequency) }}
                                            @if($schedule->frequency === 'weekly')
                                                - {{ \App\Models\BackupSchedule::getDayOfWeekOptions()[$schedule->day_of_week ?? 1] }}
                                            @elseif($schedule->frequency === 'monthly')
                                                - Tanggal {{ $schedule->day_of_month ?? 1 }}
                                            @endif
                                            pukul {{ $schedule->time ? $schedule->time->format('H:i') : '02:00' }}
                                        </p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            @if($schedule->encryption_enabled)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    <flux:icon.lock-closed class="h-3 w-3 mr-1" />
                                                    Terenkripsi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    <flux:icon.lock-open class="h-3 w-3 mr-1" />
                                                    Tidak Terenkripsi
                                                </span>
                                            @endif
                                            @if($schedule->is_active)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    <flux:icon.check-circle class="h-3 w-3 mr-1" />
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    <flux:icon.x-circle class="h-3 w-3 mr-1" />
                                                    Non-aktif
                                                </span>
                                            @endif
                                        </div>
                                        @if($schedule->last_run)
                                            <p class="text-xs text-gray-400">
                                                Terakhir dijalankan: {{ $schedule->last_run->format('d/m/Y H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- Status Toggle -->
                                @can('backup-restore.create')
                                    <flux:button
                                        size="sm"
                                        variant="{{ $schedule->is_active ? 'primary' : 'outline' }}"
                                        color="{{ $schedule->is_active ? 'green' : 'gray' }}"
                                        wire:click="toggleScheduleStatus({{ $schedule->id }})"
                                        title="{{ $schedule->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Schedule">
                                        <flux:icon.{{ $schedule->is_active ? 'check-circle' : 'x-circle' }} />
                                    </flux:button>
                                @endcan

                                <!-- Run Now -->
                                @can('backup-restore.create')
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="runScheduleNow({{ $schedule->id }})"
                                        title="Jalankan Sekarang">
                                        <flux:icon.play />
                                    </flux:button>
                                @endcan

                                <!-- Edit -->
                                @can('backup-restore.create')
                                    <flux:button
                                        size="sm"
                                        variant="outline"
                                        wire:click="openScheduleModal({{ $schedule->id }})"
                                        title="Edit Schedule">
                                        <flux:icon.pencil-square />
                                    </flux:button>
                                @endcan

                                <!-- Delete -->
                                @can('backup-restore.delete')
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        wire:click="deleteSchedule({{ $schedule->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus schedule ini?"
                                        title="Hapus Schedule">
                                        <flux:icon.trash />
                                    </flux:button>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon.calendar-days class="mx-auto h-12 w-12 text-gray-400" />
                    <flux:heading size="md" level="3" class="mt-2 text-gray-900 dark:text-white">
                        Belum ada jadwal backup
                    </flux:heading>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Buat jadwal backup otomatis untuk menjaga data tetap aman.
                    </p>
                    @can('backup-restore.create')
                        <flux:button
                            variant="primary"
                            wire:click="openScheduleModal"
                            class="mt-4"
                            icon="plus">
                            Buat Jadwal Backup
                        </flux:button>
                    @endcan
                </div>
            @endif
        </div>
    </div>

    <!-- Backup List Section -->
    <div>
        <flux:heading size="md" level="2" class="mb-4">{{ __('Daftar Backup') }}</flux:heading>

        @if(count($backups) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Nama File
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Tanggal Dibuat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Ukuran
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($backups as $backup)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $backup['name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $backup['date']->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $backup['size_human'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <!-- Download Button -->
                                            @can('backup-restore.download')
                                                <flux:button
                                                    size="sm"
                                                    variant="outline"
                                                    wire:click="downloadBackup('{{ $backup['name'] }}')"
                                                    icon="arrow-down-tray"
                                                    title="Download Backup" />
                                            @endcan

                                            <!-- Restore Button -->
                                            @can('backup-restore.restore')
                                                <flux:button
                                                    size="sm"
                                                    variant="primary"
                                                    color="green"
                                                    wire:click="confirmRestore('{{ $backup['name'] }}')"
                                                    icon="arrow-path"
                                                    title="Restore Database" />
                                            @endcan

                                            <!-- Delete Button -->
                                            @can('backup-restore.delete')
                                                <flux:button
                                                    size="sm"
                                                    variant="danger"
                                                    wire:click="deleteBackup('{{ $backup['name'] }}')"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus backup ini?"
                                                    icon="trash"
                                                    title="Hapus Backup" />
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12">
                <div class="text-center">
                    <flux:icon.archive-box class="mx-auto h-12 w-12 text-gray-400" />
                    <flux:heading size="md" level="3" class="mt-2 text-gray-900 dark:text-white">
                        Belum ada backup
                    </flux:heading>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Buat backup pertama Anda untuk memulai melindungi data.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Restore Confirmation Modal -->
    <div
        x-data="{ open: @entangle('selectedBackup').live }"
        x-show="open"
        x-cloak
        x-on:open-restore-modal.window="open = true"
        x-on:keydown.escape.window="open = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" x-on:click="open = false" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <flux:heading size="lg" level="3" class="text-gray-900 dark:text-white">
                            Konfirmasi Restore Database
                        </flux:heading>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Apakah Anda yakin ingin merestore database dari backup <strong>{{ $selectedBackup }}</strong>?
                            </p>
                            <div class="mt-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-3">
                                <div class="flex">
                                    <flux:icon.exclamation-triangle class="h-5 w-5 text-yellow-400" />
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                            <strong>Peringatan:</strong> Tindakan ini akan mengganti semua data saat ini dengan data dari backup. Pastikan Anda telah membuat backup terlebih dahulu.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    @if($restoreInProgress)
                        <flux:button
                            variant="danger"
                            wire:loading.attr="disabled"
                            disabled
                            icon="arrow-path">
                            Merestore...
                        </flux:button>
                    @else
                        @can('backup-restore.restore')
                            <flux:button
                                variant="danger"
                                wire:click="restoreBackup"
                                icon="exclamation-triangle">
                                Ya, Restore
                            </flux:button>
                        @endcan
                    @endif
                    <flux:button
                        variant="outline"
                        wire:click="cancelRestore"
                        x-on:click="open = false"
                        class="mt-3 sm:mt-0 mr-1"
                        icon="x-mark">
                        Batal
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Modal -->
    <div
        x-data="{ open: @entangle('showScheduleModal').live }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" x-on:click="open = false" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <flux:icon.calendar-days class="h-6 w-6 text-blue-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <flux:heading size="lg" level="3" class="text-gray-900 dark:text-white">
                            {{ $editingSchedule ? 'Edit Backup Schedule' : 'Buat Backup Schedule' }}
                        </flux:heading>
                        <div class="mt-2 space-y-4">
                            <!-- Name -->
                            <div>
                                <flux:label for="schedule_name">Nama Schedule</flux:label>
                                <flux:input
                                    id="schedule_name"
                                    wire:model="scheduleForm.name"
                                    placeholder="Contoh: Backup Harian"
                                    class="mt-1" />
                                @error('scheduleForm.name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Frequency -->
                            <div>
                                <flux:label for="schedule_frequency">Frekuensi</flux:label>
                                <flux:select
                                    id="schedule_frequency"
                                    wire:model.live="scheduleForm.frequency"
                                    class="mt-1">
                                    @foreach(\App\Models\BackupSchedule::getFrequencyOptions() as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                @error('scheduleForm.frequency')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Time -->
                            <div>
                                <flux:label for="schedule_time">Waktu Eksekusi</flux:label>
                                <flux:input
                                    id="schedule_time"
                                    type="time"
                                    wire:model="scheduleForm.time"
                                    class="mt-1" />
                                @error('scheduleForm.time')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Day of Week (for weekly) -->
                            @if($scheduleForm['frequency'] === 'weekly')
                                <div>
                                    <flux:label for="schedule_day_of_week">Hari dalam Seminggu</flux:label>
                                    <flux:select
                                        id="schedule_day_of_week"
                                        wire:model="scheduleForm.day_of_week"
                                        class="mt-1">
                                        @foreach(\App\Models\BackupSchedule::getDayOfWeekOptions() as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </flux:select>
                                    @error('scheduleForm.day_of_week')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <!-- Day of Month (for monthly) -->
                            @if($scheduleForm['frequency'] === 'monthly')
                                <div>
                                    <flux:label for="schedule_day_of_month">Tanggal dalam Bulan</flux:label>
                                    <flux:select
                                        id="schedule_day_of_month"
                                        wire:model="scheduleForm.day_of_month"
                                        class="mt-1">
                                        @foreach(\App\Models\BackupSchedule::getDayOfMonthOptions() as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </flux:select>
                                    @error('scheduleForm.day_of_month')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <!-- Description -->
                            <div>
                                <flux:label for="schedule_description">Deskripsi (Opsional)</flux:label>
                                <flux:textarea
                                    id="schedule_description"
                                    wire:model="scheduleForm.description"
                                    placeholder="Deskripsi schedule backup..."
                                    rows="3"
                                    class="mt-1" />
                                @error('scheduleForm.description')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Encryption -->
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <flux:checkbox
                                        id="schedule_encryption_enabled"
                                        wire:model.live="scheduleForm.encryption_enabled" />
                                    <flux:label for="schedule_encryption_enabled" class="ml-2">
                                        Aktifkan enkripsi backup
                                    </flux:label>
                                </div>

                                @if($scheduleForm['encryption_enabled'])
                                    <div>
                                        <flux:label for="schedule_encryption_password">
                                            Password Enkripsi *
                                            <span class="text-xs text-gray-500">(Minimal 8 karakter)</span>
                                        </flux:label>
                                        <flux:input
                                            id="schedule_encryption_password"
                                            type="password"
                                            wire:model="scheduleForm.encryption_password"
                                            placeholder="Masukkan password enkripsi"
                                            class="mt-1" />
                                        @error('scheduleForm.encryption_password')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <p class="text-xs text-gray-500 mt-1">
                                            ⚠️ Simpan password ini dengan aman. Diperlukan untuk membuka file backup terenkripsi.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Status -->
                            <div class="flex items-center">
                                <flux:checkbox
                                    id="schedule_is_active"
                                    wire:model="scheduleForm.is_active" />
                                <flux:label for="schedule_is_active" class="ml-2">
                                    Aktifkan schedule ini
                                </flux:label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <flux:button
                        variant="primary"
                        wire:click="saveSchedule"
                        class="sm:ml-3">
                        {{ $editingSchedule ? 'Update' : 'Buat' }} Schedule
                    </flux:button>
                    <flux:button
                        variant="outline"
                        wire:click="closeScheduleModal"
                        x-on:click="open = false">
                        Batal
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restore Password Modal -->
    <div
        x-data="{ open: @entangle('showRestorePasswordModal').live }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" x-on:click="open = false" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                        <flux:icon.lock-closed class="h-6 w-6 text-yellow-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <flux:heading size="lg" level="3" class="text-gray-900 dark:text-white">
                            File Backup Terenkripsi
                        </flux:heading>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                File backup <strong>{{ $selectedBackup }}</strong> terenkripsi. Masukkan password untuk melanjutkan restore.
                            </p>
                            <div class="mt-4">
                                <flux:label for="restore_password">Password Enkripsi</flux:label>
                                <flux:input
                                    id="restore_password"
                                    type="password"
                                    wire:model="restorePassword"
                                    placeholder="Masukkan password enkripsi"
                                    class="mt-1"
                                    autofocus />
                                @error('restorePassword')
                                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mt-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-3">
                                <div class="flex">
                                    <flux:icon.information-circle class="h-5 w-5 text-blue-400" />
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-800 dark:text-blue-200">
                                            <strong>Informasi:</strong> Password ini digunakan saat pembuatan backup schedule. Pastikan password yang Anda masukkan benar.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <flux:button
                        variant="primary"
                        wire:click="submitRestorePassword"
                        class="sm:ml-3">
                        Lanjutkan Restore
                    </flux:button>
                    <flux:button
                        variant="outline"
                        wire:click="closeRestorePasswordModal"
                        x-on:click="open = false">
                        Batal
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</div>
