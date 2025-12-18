<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\WahaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Retry up to 3 times
    public $backoff = [10, 30, 60]; // Wait 10s, 30s, 60s between retries
    public $timeout = 120; // 2 minutes timeout per job

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $messageId,
        public string $chatId,
        public string $wahaSessionName
    ) {
        // Set queue name based on session to avoid conflicts
        $this->onQueue('messages');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $message = Message::find($this->messageId);

            if (!$message) {
                Log::error('SendMessageJob: Message not found', [
                    'message_id' => $this->messageId,
                ]);
                return;
            }

            // Skip if already sent
            if ($message->status === 'sent') {
                Log::info('SendMessageJob: Message already sent', [
                    'message_id' => $this->messageId,
                ]);
                return;
            }

            // Update status to processing (optional, if you want to track this)
            // $message->update(['status' => 'processing']);

            $wahaService = new WahaService();
            $sendResult = $wahaService->sendText(
                $this->chatId,
                $message->message,
                $this->wahaSessionName
            );

            // Update message status to sent
            $message->update(['status' => 'sent']);

            // Log activity for successful message sending
            activity()
                ->performedOn($message)
                ->causedBy($message->createdBy)
                ->withProperties([
                    'attributes' => [
                        'recipient' => $this->chatId,
                        'recipient_type' => $message->group_wa_id ? 'group' : 'contact',
                        'message_type' => $message->template_id ? 'template' : 'direct',
                        'session_id' => $message->waha_session_id,
                        'template_id' => $message->template_id,
                        'job_attempt' => $this->attempts(),
                    ],
                    'ip' => request()->ip() ?? 'queue',
                    'user_agent' => request()->userAgent() ?? 'queue',
                ])
                ->log('sent a message via queue');

            Log::info('SendMessageJob: Message sent successfully', [
                'message_id' => $this->messageId,
                'recipient' => $this->chatId,
                'attempt' => $this->attempts(),
                'waha_result' => $sendResult,
            ]);

        } catch (\Exception $e) {
            Log::error('SendMessageJob: Failed to send message', [
                'message_id' => $this->messageId,
                'recipient' => $this->chatId,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update message status to failed if this is the last attempt
            if ($this->attempts() >= $this->tries) {
                $message = Message::find($this->messageId);
                if ($message) {
                    $message->update(['status' => 'failed']);

                    // Log activity for failed message sending
                    activity()
                        ->performedOn($message)
                        ->causedBy($message->createdBy)
                        ->withProperties([
                            'attributes' => [
                                'recipient' => $this->chatId,
                                'recipient_type' => $message->group_wa_id ? 'group' : 'contact',
                                'message_type' => $message->template_id ? 'template' : 'direct',
                                'session_id' => $message->waha_session_id,
                                'error' => $e->getMessage(),
                                'final_attempt' => true,
                            ],
                            'ip' => request()->ip() ?? 'queue',
                            'user_agent' => request()->userAgent() ?? 'queue',
                        ])
                        ->log('failed to send message after retries');
                }
            }

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $message = Message::find($this->messageId);
        if ($message && $message->status !== 'sent') {
            $message->update(['status' => 'failed']);

            Log::error('SendMessageJob: Job failed permanently', [
                'message_id' => $this->messageId,
                'recipient' => $this->chatId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}

