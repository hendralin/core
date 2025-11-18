<?php

namespace Database\Seeders;

use App\Models\BackupSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DefaultBackupScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default backup schedules if they don't exist
        $schedules = [
            [
                'name' => 'Backup Harian',
                'frequency' => 'daily',
                'time' => '02:00',
                'is_active' => true,
                'description' => 'Backup database harian pukul 02:00'
            ],
            [
                'name' => 'Backup Mingguan',
                'frequency' => 'weekly',
                'time' => '03:00',
                'day_of_week' => 1, // Monday
                'is_active' => false,
                'description' => 'Backup database mingguan setiap Senin pukul 03:00',
                'encryption_enabled' => false,
                'encryption_password' => null
            ]
        ];

        foreach ($schedules as $scheduleData) {
            BackupSchedule::firstOrCreate(
                ['name' => $scheduleData['name']],
                $scheduleData
            );
        }

        $this->command->info('Default backup schedules created successfully!');
        $this->command->info('');
        $this->command->info('To enable automatic backups, add this to your crontab:');
        $this->command->info('* * * * * cd ' . base_path() . ' && php artisan backup:run-auto >> /dev/null 2>&1');
        $this->command->info('');
        $this->command->info('Or for Windows Task Scheduler:');
        $this->command->info('schtasks /create /tn "AutoBackup" /tr "php ' . base_path() . '\artisan backup:run-auto" /sc minute /mo 1');
    }
}
