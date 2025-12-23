<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\Message;
use App\Models\Session;
use App\Jobs\SendMessageJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessScheduledMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:process {--schedule= : Specific schedule ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled messages and send them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scheduleId = $this->option('schedule');

        if ($scheduleId) {
            // Process specific schedule
            $schedule = Schedule::find($scheduleId);
            if (!$schedule) {
                $this->error("Schedule with ID {$scheduleId} not found.");
                return Command::FAILURE;
            }

            return $this->processSchedule($schedule);
        }

        // Process all active schedules that should run now
        // Compare in UTC for consistency
        $schedules = Schedule::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('next_run')
                    ->orWhere('next_run', '<=', Carbon::now('UTC'));
            })
            ->get();

        $processedCount = 0;
        $errorCount = 0;

        foreach ($schedules as $schedule) {
            $this->info("Processing schedule: {$schedule->name}");
            $result = $this->processSchedule($schedule);
            if ($result === Command::SUCCESS) {
                $processedCount++;
            } else {
                $errorCount++;
            }
        }

        if ($processedCount === 0 && $errorCount === 0) {
            $this->info('No schedules need to be processed at this time.');
        } else {
            $this->info("Successfully processed {$processedCount} schedule(s).");
            if ($errorCount > 0) {
                $this->warn("Failed to process {$errorCount} schedule(s).");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Process a specific schedule
     */
    private function processSchedule(Schedule $schedule): int
    {
        try {
            // Check if schedule should run
            if (!$schedule->shouldRunNow()) {
                $this->warn("Schedule '{$schedule->name}' is not ready to run yet.");
                return Command::SUCCESS;
            }

            // Get the WAHA session
            $session = $schedule->wahaSession;
            if (!$session) {
                $this->error("Session not found for schedule '{$schedule->name}'.");
                return Command::FAILURE;
            }

            $wahaSessionName = $session->session_id;
            if (!$wahaSessionName) {
                $this->error("Invalid session for schedule '{$schedule->name}'.");
                return Command::FAILURE;
            }

            // Determine recipient - use group_wa_id, received_number, or wa_id
            $chatId = null;
            $waId = null;
            $groupWaId = null;
            $receivedNumber = null;

            if ($schedule->group_wa_id) {
                $chatId = $schedule->group_wa_id;
                $groupWaId = $schedule->group_wa_id;
            } elseif ($schedule->wa_id) {
                // wa_id is already in WAHA format (with @s.whatsapp.net)
                $chatId = $schedule->wa_id;
                $waId = $schedule->wa_id;
                $receivedNumber = $schedule->received_number ?? preg_replace('/@.+$/', '', $schedule->wa_id);
            } elseif ($schedule->received_number) {
                // received_number is the original number, need to format for WAHA
                $cleanNumber = preg_replace('/@.+$/', '', $schedule->received_number);
                $cleanNumber = preg_replace('/[^\d+]/', '', $cleanNumber);
                $cleanNumber = ltrim($cleanNumber, '+');
                $chatId = $cleanNumber . '@s.whatsapp.net';
                $waId = $chatId;
                $receivedNumber = $schedule->received_number;
            }

            if (!$chatId) {
                $this->error("No valid recipient found for schedule '{$schedule->name}'.");
                return Command::FAILURE;
            }

            // Create message record
            DB::beginTransaction();

            $message = Message::create([
                'waha_session_id' => $schedule->waha_session_id,
                'template_id' => null,
                'wa_id' => $waId,
                'group_wa_id' => $groupWaId,
                'received_number' => $receivedNumber,
                'message' => $schedule->message,
                'status' => 'pending',
                'scheduled_at' => $schedule->next_run,
                'created_by' => $schedule->created_by,
            ]);

            // Dispatch job to send message
            SendMessageJob::dispatch($message->id, $chatId, $wahaSessionName, 'text')
                ->onQueue('messages');

            // Mark schedule as run and calculate next run
            $schedule->markAsRun();

            DB::commit();

            $this->info("✓ Message queued for schedule '{$schedule->name}' (Message ID: {$message->id})");

            Log::info('Scheduled message processed', [
                'schedule_id' => $schedule->id,
                'schedule_name' => $schedule->name,
                'message_id' => $message->id,
                'recipient' => $chatId,
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error("✗ Error processing schedule '{$schedule->name}': {$e->getMessage()}");

            Log::error('Failed to process scheduled message', [
                'schedule_id' => $schedule->id,
                'schedule_name' => $schedule->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}

