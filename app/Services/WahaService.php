<?php

namespace App\Services;

use App\Models\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaService
{
    protected ?string $baseUrl = null;
    protected ?string $apiKey = null;
    protected string $session;
    public bool $isConnected = false;
    protected ?int $userId = null;

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId ?? Auth::id();
        $this->loadConfig();
        $this->session = env('WAHA_SESSION_ID', 'The_Broadcaster');
    }

    /**
     * Load configuration from database for the user
     */
    protected function loadConfig(): void
    {
        if ($this->userId) {
            $config = Config::where('user_id', $this->userId)->first();
            if ($config) {
                $this->baseUrl = $config->api_url;
                $this->apiKey = $config->api_key;
            }
        }
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
     * Send file message via WAHA API (generic method for image and file)
     *
     * @param string $chatId WhatsApp chat ID (e.g., "6281234567890@s.whatsapp.net")
     * @param string $fileUrl URL of the file to send
     * @param string $endpoint API endpoint ('/api/sendImage' or '/api/sendFile')
     * @param string|null $caption Optional caption for the file
     * @param string|null $session WAHA session name (optional, defaults to config)
     * @param string|null $mimetype MIME type of the file
     * @param string|null $filename Filename of the file (optional)
     * @return array Response from WAHA API
     * @throws \Exception
     */
    private function sendFileGeneric(string $chatId, string $fileUrl, string $endpoint, ?string $caption = null, ?string $session = null, ?string $mimetype = null, ?string $filename = null): array
    {
        $sessionName = $session ?? $this->session;

        try {
            // Determine mimetype from URL if not provided
            if (!$mimetype) {
                $extension = strtolower(pathinfo(parse_url($fileUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                $mimeTypes = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xls' => 'application/vnd.ms-excel',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'zip' => 'application/zip',
                    'rar' => 'application/x-rar-compressed',
                    '7z' => 'application/x-7z-compressed',
                    'tar' => 'application/x-tar',
                    'gz' => 'application/gzip',
                    'bz2' => 'application/x-bzip2',
                    'xz' => 'application/x-xz',
                    'wim' => 'application/x-wim',
                    'iso' => 'application/x-iso9660-image',
                    'mp4' => 'video/mp4',
                    'mov' => 'video/quicktime',
                    'avi' => 'video/x-msvideo',
                    'mkv' => 'video/x-matroska',
                    'webm' => 'video/webm',
                    'ogg' => 'video/ogg',
                    'mp3' => 'audio/mpeg',
                    'wav' => 'audio/wav',
                    'ogg' => 'audio/ogg',
                ];
                // Default mimetype based on endpoint
                $defaultMimetype = str_contains($endpoint, 'Image') ? 'image/jpeg' : 'application/octet-stream';
                $mimetype = $mimeTypes[$extension] ?? $defaultMimetype;
            }

            // Determine filename from URL if not provided
            if (!$filename) {
                $defaultFilename = str_contains($endpoint, 'Image') ? 'image.jpg' : 'file';
                $filename = basename(parse_url($fileUrl, PHP_URL_PATH)) ?: $defaultFilename;
            }

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60) // Longer timeout for file uploads
            ->post($this->baseUrl . $endpoint, [
                'chatId' => $chatId,
                'file' => [
                    'mimetype' => $mimetype,
                    'filename' => $filename,
                    'url' => $fileUrl,
                ],
                'reply_to' => null,
                'caption' => $caption ?? '',
                'session' => $sessionName,
            ]);

            if ($response->successful()) {
                $logType = str_contains($endpoint, 'Image') ? 'sendImage' : 'sendFile';
                Log::info("WAHA {$logType} success", [
                    'session' => $sessionName,
                    'chatId' => $chatId,
                    'fileUrl' => $fileUrl,
                    'caption' => $caption,
                    'response' => $response->json(),
                ]);

                return $response->json();
            }

            // Log error response
            $logType = str_contains($endpoint, 'Image') ? 'sendImage' : 'sendFile';
            Log::error("WAHA {$logType} failed", [
                'session' => $sessionName,
                'chatId' => $chatId,
                'fileUrl' => $fileUrl,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('WAHA API returned status ' . $response->status() . ': ' . $response->body());

        } catch (\Exception $e) {
            $logType = str_contains($endpoint, 'Image') ? 'sendImage' : 'sendFile';
            Log::error("WAHA {$logType} exception", [
                'session' => $sessionName,
                'chatId' => $chatId,
                'fileUrl' => $fileUrl,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send image message via WAHA API
     *
     * @param string $chatId WhatsApp chat ID (e.g., "6281234567890@s.whatsapp.net")
     * @param string $imageUrl URL of the image to send
     * @param string|null $caption Optional caption for the image
     * @param string|null $session WAHA session name (optional, defaults to config)
     * @param string|null $mimetype MIME type of the image (default: "image/jpeg")
     * @param string|null $filename Filename of the image (optional)
     * @return array Response from WAHA API
     * @throws \Exception
     */
    public function sendImage(string $chatId, string $imageUrl, ?string $caption = null, ?string $session = null, ?string $mimetype = null, ?string $filename = null): array
    {
        return $this->sendFileGeneric($chatId, $imageUrl, '/api/sendImage', $caption, $session, $mimetype, $filename);
    }

    /**
     * Send file message via WAHA API
     *
     * @param string $chatId WhatsApp chat ID (e.g., "6281234567890@s.whatsapp.net")
     * @param string $fileUrl URL of the file to send
     * @param string|null $caption Optional caption for the file
     * @param string|null $session WAHA session name (optional, defaults to config)
     * @param string|null $mimetype MIME type of the file
     * @param string|null $filename Filename of the file (optional)
     * @return array Response from WAHA API
     * @throws \Exception
     */
    public function sendFile(string $chatId, string $fileUrl, ?string $caption = null, ?string $session = null, ?string $mimetype = null, ?string $filename = null): array
    {
        return $this->sendFileGeneric($chatId, $fileUrl, '/api/sendFile', $caption, $session, $mimetype, $filename);
    }

    /**
     * Send text message with custom link preview via WAHA API
     *
     * @param string $chatId WhatsApp chat ID (e.g., "6281234567890@s.whatsapp.net")
     * @param string $text Message text
     * @param string $previewUrl URL for the preview
     * @param string|null $previewTitle Optional title for the preview
     * @param string|null $previewDescription Optional description for the preview
     * @param string|null $previewImageUrl Optional image URL for the preview
     * @param string|null $session WAHA session name (optional, defaults to config)
     * @param bool $linkPreviewHighQuality Whether to use high quality link preview (default: true)
     * @return array Response from WAHA API
     * @throws \Exception
     */
    public function sendCustomLinkPreview(string $chatId, string $text, string $previewUrl, ?string $previewTitle = null, ?string $previewDescription = null, ?string $previewImageUrl = null, ?string $session = null, bool $linkPreviewHighQuality = true): array
    {
        $sessionName = $session ?? $this->session;

        try {
            // Build preview object
            $preview = [
                'url' => $previewUrl,
            ];

            if ($previewTitle) {
                $preview['title'] = $previewTitle;
            }

            if ($previewDescription) {
                $preview['description'] = $previewDescription;
            }

            if ($previewImageUrl) {
                $preview['image'] = [
                    'url' => $previewImageUrl,
                ];
            }

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)
            ->post($this->baseUrl . '/api/send/link-custom-preview', [
                'chatId' => $chatId,
                'text' => $text,
                'reply_to' => null,
                'linkPreviewHighQuality' => $linkPreviewHighQuality,
                'preview' => $preview,
                'session' => $sessionName,
            ]);

            if ($response->successful()) {
                Log::info('WAHA sendCustomLinkPreview success', [
                    'session' => $sessionName,
                    'chatId' => $chatId,
                    'text' => substr($text, 0, 100) . '...',
                    'previewUrl' => $previewUrl,
                    'response' => $response->json(),
                ]);

                return $response->json();
            }

            // Log error response
            Log::error('WAHA sendCustomLinkPreview failed', [
                'session' => $sessionName,
                'chatId' => $chatId,
                'text' => substr($text, 0, 100) . '...',
                'previewUrl' => $previewUrl,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception('WAHA API returned status ' . $response->status() . ': ' . $response->body());

        } catch (\Exception $e) {
            Log::error('WAHA sendCustomLinkPreview exception', [
                'session' => $sessionName,
                'chatId' => $chatId,
                'text' => substr($text, 0, 100) . '...',
                'previewUrl' => $previewUrl,
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
            if ($this->baseUrl && $this->apiKey) {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'X-Api-Key' => $this->apiKey,
                ])->get($this->baseUrl . '/health');

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
