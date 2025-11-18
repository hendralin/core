<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BackupSchedule;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class RunAutoBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run-auto {--schedule= : Specific schedule ID to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run scheduled automatic backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scheduleId = $this->option('schedule');

        if ($scheduleId) {
            // Run specific schedule
            $schedule = BackupSchedule::find($scheduleId);
            if (!$schedule) {
                $this->error("Schedule with ID {$scheduleId} not found.");
                return Command::FAILURE;
            }

            return $this->runSchedule($schedule);
        }

        // Run all active schedules that should run now
        $schedules = BackupSchedule::where('is_active', true)->get();
        $runCount = 0;

        foreach ($schedules as $schedule) {
            if ($schedule->shouldRunNow()) {
                $this->info("Running backup schedule: {$schedule->name}");
                $result = $this->runSchedule($schedule);
                if ($result === Command::SUCCESS) {
                    $runCount++;
                }
            }
        }

        if ($runCount === 0) {
            $this->info('No backup schedules need to run at this time.');
        } else {
            $this->info("Successfully ran {$runCount} backup schedule(s).");
        }

        return Command::SUCCESS;
    }

    /**
     * Run a specific backup schedule
     */
    private function runSchedule(BackupSchedule $schedule): int
    {
        try {
            $this->info("Starting backup for schedule: {$schedule->name}");

            // Set encryption password if enabled for this schedule
            if ($schedule->encryption_enabled && $schedule->encryption_password) {
                config(['backup.backup.password' => $schedule->encryption_password]);
            } else {
                config(['backup.backup.password' => null]);
            }

            // Run the backup using Spatie Backup
            $exitCode = Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true,
            ]);

            if ($exitCode === 0) {
                $schedule->markAsRun();
                $this->info("✓ Backup completed successfully for: {$schedule->name}");
                return Command::SUCCESS;
            } else {
                $this->error("✗ Backup failed for: {$schedule->name}");
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("✗ Error running backup for {$schedule->name}: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
