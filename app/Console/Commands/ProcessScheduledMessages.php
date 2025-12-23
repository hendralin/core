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

            DB::beginTransaction();

            // Get recipients from pivot table (new way) or legacy fields (backward compatibility)
            $recipients = $schedule->recipients;

            // If no recipients in pivot table, check legacy fields for backward compatibility
            if ($recipients->isEmpty()) {
                // Legacy support: create recipient data from old fields
                $legacyRecipients = [];

                if ($schedule->group_wa_id) {
                    $legacyRecipients[] = [
                        'recipient_type' => 'group',
                        'group_wa_id' => $schedule->group_wa_id,
                        'chat_id' => $schedule->group_wa_id,
                    ];
                } elseif ($schedule->wa_id) {
                    $legacyRecipients[] = [
                        'recipient_type' => $schedule->received_number ? 'contact' : 'number',
                        'wa_id' => $schedule->wa_id,
                        'received_number' => $schedule->received_number ?? preg_replace('/@.+$/', '', $schedule->wa_id),
                        'chat_id' => $schedule->wa_id,
                    ];
                } elseif ($schedule->received_number) {
                    $cleanNumber = preg_replace('/@.+$/', '', $schedule->received_number);
                    $cleanNumber = preg_replace('/[^\d+]/', '', $cleanNumber);
                    $cleanNumber = ltrim($cleanNumber, '+');
                    $chatId = $cleanNumber . '@s.whatsapp.net';
                    $legacyRecipients[] = [
                        'recipient_type' => 'number',
                        'wa_id' => $chatId,
                        'received_number' => $schedule->received_number,
                        'chat_id' => $chatId,
                    ];
                }

                if (empty($legacyRecipients)) {
                    DB::rollBack();
                    $this->error("No valid recipient found for schedule '{$schedule->name}'.");
                    return Command::FAILURE;
                }

                // Process legacy recipients
                foreach ($legacyRecipients as $recipientData) {
                    $this->sendToRecipient($schedule, $wahaSessionName, $recipientData);
                }
            } else {
                // Process recipients from pivot table
                foreach ($recipients as $recipient) {
                    $chatId = null;
                    $waId = null;
                    $groupWaId = null;
                    $receivedNumber = null;

                    if ($recipient->recipient_type === 'group' && $recipient->group_wa_id) {
                        $chatId = $recipient->group_wa_id;
                        $groupWaId = $recipient->group_wa_id;
                    } elseif ($recipient->wa_id) {
                        $chatId = $recipient->wa_id;
                        $waId = $recipient->wa_id;
                        $receivedNumber = $recipient->received_number ?? preg_replace('/@.+$/', '', $recipient->wa_id);
                    }

                    if ($chatId) {
                        $recipientData = [
                            'recipient_type' => $recipient->recipient_type,
                            'wa_id' => $waId,
                            'group_wa_id' => $groupWaId,
                            'received_number' => $receivedNumber,
                            'chat_id' => $chatId,
                        ];

                        $this->sendToRecipient($schedule, $wahaSessionName, $recipientData);
                    }
                }
            }

            // Mark schedule as run and calculate next run
            $schedule->markAsRun();

            DB::commit();

            $this->info("✓ Messages queued for schedule '{$schedule->name}'");

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

    /**
     * Send message to a single recipient
     */
    private function sendToRecipient(Schedule $schedule, string $wahaSessionName, array $recipientData): void
    {
        // Create message record
        $message = Message::create([
            'waha_session_id' => $schedule->waha_session_id,
            'template_id' => null,
            'wa_id' => $recipientData['wa_id'] ?? null,
            'group_wa_id' => $recipientData['group_wa_id'] ?? null,
            'received_number' => $recipientData['received_number'] ?? null,
            'message' => $schedule->message,
            'status' => 'pending',
            'scheduled_at' => $schedule->next_run,
            'created_by' => $schedule->created_by,
        ]);

        // Dispatch job to send message
        SendMessageJob::dispatch($message->id, $recipientData['chat_id'], $wahaSessionName, 'text')
            ->onQueue('messages');

        $this->info("  → Message queued for {$recipientData['recipient_type']} (Message ID: {$message->id})");

        Log::info('Scheduled message recipient processed', [
            'schedule_id' => $schedule->id,
            'schedule_name' => $schedule->name,
            'message_id' => $message->id,
            'recipient_type' => $recipientData['recipient_type'],
            'recipient' => $recipientData['chat_id'],
        ]);
    }
}

