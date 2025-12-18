<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $session;
    public bool $isConnected = false;

    public function __construct()
    {
        $this->baseUrl = env('WAHA_API_URL');
        $this->apiKey = env('WAHA_API_KEY');
        $this->session = env('WAHA_SESSION_ID', 'The_Broadcaster');
    }

    /**
     * Start typing indicator in chat
     *
     * @param string $chatId WhatsApp chat ID
     * @param string|null $session WAHA session name (optional)
     * @return array Response from WAHA API
     * @throws \Exception
     */
    public function startTyping(string $chatId, ?string $session = null): array
    {
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/startTyping', [
                'chatId' => $chatId,
                'session' => $session ?? $this->session,
            ]);

            if ($response->successful()) {
                Log::info('WAHA startTyping success', [
                    'chatId' => $chatId,
                    'session' => $session ?? $this->session,
                ]);

                return $response->json();
            }

            Log::warning('WAHA startTyping failed', [
                'chatId' => $chatId,
                'session' => $session ?? $this->session,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            // Don't throw exception for typing indicators, just log
            return ['success' => false, 'error' => 'Failed to start typing'];

        } catch (\Exception $e) {
            Log::warning('WAHA startTyping exception', [
                'chatId' => $chatId,
                'session' => $session ?? $this->session,
                'error' => $e->getMessage(),
            ]);

            // Don't throw exception for typing indicators
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Stop typing indicator in chat
     *
     * @param string $chatId WhatsApp chat ID
     * @param string|null $session WAHA session name (optional)
     * @return array Response from WAHA API
     * @throws \Exception
     */
    public function stopTyping(string $chatId, ?string $session = null): array
    {
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/stopTyping', [
                'chatId' => $chatId,
                'session' => $session ?? $this->session,
            ]);

            if ($response->successful()) {
                Log::info('WAHA stopTyping success', [
                    'chatId' => $chatId,
                    'session' => $session ?? $this->session,
                ]);

                return $response->json();
            }

            Log::warning('WAHA stopTyping failed', [
                'chatId' => $chatId,
                'session' => $session ?? $this->session,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            // Don't throw exception for typing indicators, just log
            return ['success' => false, 'error' => 'Failed to stop typing'];

        } catch (\Exception $e) {
            Log::warning('WAHA stopTyping exception', [
                'chatId' => $chatId,
                'session' => $session ?? $this->session,
                'error' => $e->getMessage(),
            ]);

            // Don't throw exception for typing indicators
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send text message with typing indicators via WAHA API
     *
     * @param string $chatId WhatsApp chat ID (e.g., "6281234567890@s.whatsapp.net")
     * @param string $text Message text
     * @param string|null $session WAHA session name (optional, defaults to config)
     * @param bool $useTypingIndicator Whether to use typing indicator (default: true)
     * @param float $typingDelay Delay in seconds for typing indicator (default: 0.5)
     * @return array Response from WAHA API
     * @throws \Exception
     */
    public function sendText(string $chatId, string $text, ?string $session = null, bool $useTypingIndicator = true, float $typingDelay = 0.5): array
    {
        $sessionName = $session ?? $this->session;

        try {
            // Start typing indicator before sending message (optional, can be disabled for bulk)
            if ($useTypingIndicator) {
                $this->startTyping($chatId, $sessionName);
                // Reduced delay for better performance (0.5s instead of 1s)
                usleep((int)($typingDelay * 1000000));
            }

            // Send the actual message
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30) // Add timeout to prevent hanging
            ->post($this->baseUrl . '/api/sendText', [
                'chatId' => $chatId,
                'reply_to' => null,
                'text' => $text,
                'linkPreview' => true,
                'linkPreviewHighQuality' => false,
                'session' => $sessionName,
            ]);

            if ($response->successful()) {
                // Stop typing indicator after successful send
                if ($useTypingIndicator) {
                    $this->stopTyping($chatId, $sessionName);
                }

                Log::info('WAHA sendText success', [
                    'session' => $sessionName,
                    'chatId' => $chatId,
                    'text' => substr($text, 0, 100) . '...', // Log only first 100 chars
                    'response' => $response->json(),
                ]);

                return $response->json();
            }

            // Stop typing indicator even on failure
            if ($useTypingIndicator) {
                $this->stopTyping($chatId, $sessionName);
            }

            // Log error response
            Log::error('WAHA sendText failed', [
                'session' => $sessionName,
                'chatId' => $chatId,
                'text' => substr($text, 0, 100) . '...',
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('WAHA API returned status ' . $response->status() . ': ' . $response->body());

        } catch (\Exception $e) {
            // Stop typing indicator on any exception
            if ($useTypingIndicator) {
                $this->stopTyping($chatId, $sessionName);
            }

            Log::error('WAHA sendText exception', [
                'session' => $sessionName,
                'chatId' => $chatId,
                'text' => substr($text, 0, 100) . '...',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send text message to multiple recipients with rate limiting
     *
     * @param array $recipients Array of ['chatId' => string, 'text' => string]
     * @param string|null $session Custom session (optional)
     * @param int $rateLimitPerSecond Maximum messages per second (default: 5)
     * @param bool $useTypingIndicator Whether to use typing indicator (default: false for bulk)
     * @return array Array of results for each recipient
     */
    public function sendBulkText(array $recipients, ?string $session = null, int $rateLimitPerSecond = 5, bool $useTypingIndicator = false): array
    {
        $results = [];
        $startTime = microtime(true);
        $messageCount = 0;
        $delayBetweenMessages = 1.0 / $rateLimitPerSecond; // Calculate delay to maintain rate limit

        foreach ($recipients as $recipient) {
            try {
                // Rate limiting: ensure we don't exceed messages per second
                $currentTime = microtime(true);
                $elapsed = $currentTime - $startTime;
                $expectedTime = $messageCount * $delayBetweenMessages;

                if ($elapsed < $expectedTime) {
                    $sleepTime = $expectedTime - $elapsed;
                    usleep((int)($sleepTime * 1000000));
                }

                // sendText with typing indicator disabled for bulk sending
                $result = $this->sendText(
                    $recipient['chatId'],
                    $recipient['text'],
                    $session,
                    $useTypingIndicator,
                    0.3 // Reduced delay for bulk
                );

                $results[] = [
                    'chatId' => $recipient['chatId'],
                    'success' => true,
                    'result' => $result,
                ];

                $messageCount++;

            } catch (\Exception $e) {
                $results[] = [
                    'chatId' => $recipient['chatId'],
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
                $messageCount++; // Count failed messages too for rate limiting
            }
        }

        return $results;
    }

    /**
     * Validate chat ID format
     *
     * @param string $chatId
     * @return bool
     */
    public function isValidChatId(string $chatId): bool
    {
        // Basic validation for WhatsApp chat ID format
        return preg_match('/^\d+@s\.whatsapp\.net$/', $chatId) ||
               preg_match('/^\d+@g\.us$/', $chatId); // for groups
    }

    /**
     * Check connection to WAHA API
     *
     * @return bool
     */
    public function checkConnection()
    {
        try {
            if (env('WAHA_API_URL') && env('WAHA_API_KEY')) {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'X-Api-Key' => env('WAHA_API_KEY'),
                ])->get(env('WAHA_API_URL') . '/health');

                $this->isConnected = $response->successful() && $response->status() === 200;
            } else {
                $this->isConnected = false;
            }
        } catch (\Exception $e) {
            $this->isConnected = false;
            Log::warning('Failed to check WAHA connection: ' . $e->getMessage());
        }
    }
}
