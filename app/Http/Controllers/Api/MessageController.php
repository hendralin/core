<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Message;
use App\Models\Session;
use App\Models\Template;
use App\Jobs\SendMessageJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\SendFileMessageRequest;
use App\Http\Requests\Api\SendTextMessageRequest;
use App\Http\Requests\Api\SendImageMessageRequest;
use App\Http\Requests\Api\SendTemplateMessageRequest;
use App\Http\Requests\Api\SendCustomLinkMessageRequest;

class MessageController extends Controller
{
    /**
     * Send a text message via API
     *
     * @param SendTextMessageRequest $request
     * @return JsonResponse
     */
    public function sendText(SendTextMessageRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Get the WAHA session
            $sessionRecord = Session::where('created_by', Auth::id())
                ->where('session_id', $request->session_id)
                ->first();

            if (!$sessionRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found or you do not have access to this session.',
                ], 404);
            }

            $wahaSessionName = $sessionRecord->session_id;

            if (!$wahaSessionName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session configuration.',
                ], 400);
            }

            // Clean and format phone number
            $cleanNumber = preg_replace('/[^\d+]/', '', $request->phone_number);
            $cleanNumber = ltrim($cleanNumber, '+');
            $waId = $cleanNumber . '@s.whatsapp.net';

            // Determine chat ID (for group or contact)
            $chatId = $request->group_wa_id ?? $waId;

            // Handle scheduled sending
            $scheduledAt = null;
            if ($request->scheduled_at) {
                $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                $scheduledAt = Carbon::parse($request->scheduled_at, $userTimezone)->utc();
            }

            // Create message record with 'pending' status
            $message = Message::create([
                'waha_session_id' => $sessionRecord->id,
                'template_id' => null, // Text message doesn't use template
                'wa_id' => $request->group_wa_id ? null : $waId,
                'group_wa_id' => $request->group_wa_id ?? null,
                'received_number' => $request->phone_number,
                'message' => $request->message,
                'status' => 'pending',
                'scheduled_at' => $scheduledAt,
                'created_by' => Auth::id(),
            ]);

            // Dispatch job to send message asynchronously
            $job = SendMessageJob::dispatch($message->id, $chatId, $wahaSessionName, 'text')
                ->onQueue('messages');

            // Schedule for later if provided
            if ($scheduledAt) {
                $job->delay($scheduledAt);
            }

            // Log activity
            activity()
                ->performedOn($message)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'recipient' => $chatId,
                        'recipient_type' => $request->group_wa_id ? 'group' : 'contact',
                        'message_type' => 'direct',
                        'session_id' => $request->session_id,
                        'status' => 'pending',
                    ],
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('queued a message for sending via API');

            DB::commit();

            Log::info('Message queued for sending via API', [
                'message_id' => $message->id,
                'recipient' => $chatId,
                'session' => $wahaSessionName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message has been queued for sending.',
                'data' => [
                    'id' => $message->id,
                    'status' => $message->status,
                    'scheduled_at' => $message->scheduled_at?->toIso8601String(),
                    'created_at' => $message->created_at->toIso8601String(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to queue message via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send an image message (URL-based) with caption via API.
     *
     * @param SendImageMessageRequest $request
     * @return JsonResponse
     */
    public function sendImage(SendImageMessageRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Get the WAHA session
            $sessionRecord = Session::where('created_by', Auth::id())
                ->where('session_id', $request->session_id)
                ->first();

            if (!$sessionRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found or you do not have access to this session.',
                ], 404);
            }

            $wahaSessionName = $sessionRecord->session_id;

            if (!$wahaSessionName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session configuration.',
                ], 400);
            }

            // Clean and format phone number
            $waId = null;
            $chatId = null;

            if ($request->phone_number) {
                $cleanNumber = preg_replace('/[^\d+]/', '', $request->phone_number);
                $cleanNumber = ltrim($cleanNumber, '+');
                $waId = $cleanNumber . '@s.whatsapp.net';
                $chatId = $waId;
            }

            if ($request->group_wa_id) {
                $chatId = $request->group_wa_id;
            }

            // Handle scheduled sending
            $scheduledAt = null;
            if ($request->scheduled_at) {
                $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                $scheduledAt = Carbon::parse($request->scheduled_at, $userTimezone)->utc();
            }

            // Prepare message payload for image
            $messageData = json_encode([
                'type' => 'image',
                'url' => $request->image_url,
                'caption' => $request->caption ?? '',
                'mimetype' => $request->mimetype ?? null,
                'filename' => $request->filename ?? null,
            ]);

            // Create message record with 'pending' status
            $message = Message::create([
                'waha_session_id' => $sessionRecord->id,
                'template_id' => null,
                'wa_id' => $request->group_wa_id ? null : $waId,
                'group_wa_id' => $request->group_wa_id ?? null,
                'received_number' => $request->phone_number,
                'message' => $messageData,
                'status' => 'pending',
                'scheduled_at' => $scheduledAt,
                'created_by' => Auth::id(),
            ]);

            // Dispatch job to send message asynchronously
            $job = SendMessageJob::dispatch($message->id, $chatId, $wahaSessionName, 'image')
                ->onQueue('messages');

            // Schedule for later if provided
            if ($scheduledAt) {
                $job->delay($scheduledAt);
            }

            // Log activity
            activity()
                ->performedOn($message)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'recipient' => $chatId,
                        'recipient_type' => $request->group_wa_id ? 'group' : 'contact',
                        'message_type' => 'direct',
                        'session_id' => $sessionRecord->id,
                        'status' => 'pending',
                        'content_type' => 'image',
                    ],
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('queued an image message via API');

            DB::commit();

            Log::info('Image message queued for sending via API', [
                'message_id' => $message->id,
                'recipient' => $chatId,
                'session' => $wahaSessionName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image message has been queued for sending.',
                'data' => [
                    'id' => $message->id,
                    'status' => $message->status,
                    'scheduled_at' => $message->scheduled_at?->toIso8601String(),
                    'created_at' => $message->created_at->toIso8601String(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to queue image message via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue image message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a file message (URL-based) with caption via API.
     *
     * @param SendFileMessageRequest $request
     * @return JsonResponse
     */
    public function sendFile(SendFileMessageRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Get the WAHA session
            $sessionRecord = Session::where('created_by', Auth::id())
                ->where('session_id', $request->session_id)
                ->first();

            if (!$sessionRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found or you do not have access to this session.',
                ], 404);
            }

            $wahaSessionName = $sessionRecord->session_id;

            if (!$wahaSessionName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session configuration.',
                ], 400);
            }

            // Clean and format phone number
            $waId = null;
            $chatId = null;

            if ($request->phone_number) {
                $cleanNumber = preg_replace('/[^\d+]/', '', $request->phone_number);
                $cleanNumber = ltrim($cleanNumber, '+');
                $waId = $cleanNumber . '@s.whatsapp.net';
                $chatId = $waId;
            }

            if ($request->group_wa_id) {
                $chatId = $request->group_wa_id;
            }

            // Handle scheduled sending
            $scheduledAt = null;
            if ($request->scheduled_at) {
                $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                $scheduledAt = Carbon::parse($request->scheduled_at, $userTimezone)->utc();
            }

            // Prepare message payload for file
            $messageData = json_encode([
                'type' => 'file',
                'url' => $request->file_url,
                'caption' => $request->caption ?? '',
                'mimetype' => $request->mimetype ?? null,
                'filename' => $request->filename ?? null,
            ]);

            // Create message record with 'pending' status
            $message = Message::create([
                'waha_session_id' => $sessionRecord->id,
                'template_id' => null,
                'wa_id' => $request->group_wa_id ? null : $waId,
                'group_wa_id' => $request->group_wa_id ?? null,
                'received_number' => $request->phone_number,
                'message' => $messageData,
                'status' => 'pending',
                'scheduled_at' => $scheduledAt,
                'created_by' => Auth::id(),
            ]);

            // Dispatch job to send message asynchronously
            $job = SendMessageJob::dispatch($message->id, $chatId, $wahaSessionName, 'file')
                ->onQueue('messages');

            // Schedule for later if provided
            if ($scheduledAt) {
                $job->delay($scheduledAt);
            }

            // Log activity
            activity()
                ->performedOn($message)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'recipient' => $chatId,
                        'recipient_type' => $request->group_wa_id ? 'group' : 'contact',
                        'message_type' => 'direct',
                        'session_id' => $sessionRecord->id,
                        'status' => 'pending',
                        'content_type' => 'file',
                    ],
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('queued a file message via API');

            DB::commit();

            Log::info('File message queued for sending via API', [
                'message_id' => $message->id,
                'recipient' => $chatId,
                'session' => $wahaSessionName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File message has been queued for sending.',
                'data' => [
                    'id' => $message->id,
                    'status' => $message->status,
                    'scheduled_at' => $message->scheduled_at?->toIso8601String(),
                    'created_at' => $message->created_at->toIso8601String(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to queue file message via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue file message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a custom link preview message via API.
     *
     * @param SendCustomLinkMessageRequest $request
     * @return JsonResponse
     */
    public function sendCustomLink(SendCustomLinkMessageRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Get the WAHA session
            $sessionRecord = Session::where('created_by', Auth::id())
                ->where('session_id', $request->session_id)
                ->first();

            if (!$sessionRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found or you do not have access to this session.',
                ], 404);
            }

            $wahaSessionName = $sessionRecord->session_id;

            if (!$wahaSessionName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session configuration.',
                ], 400);
            }

            // Clean and format phone number
            $waId = null;
            $chatId = null;

            if ($request->phone_number) {
                $cleanNumber = preg_replace('/[^\d+]/', '', $request->phone_number);
                $cleanNumber = ltrim($cleanNumber, '+');
                $waId = $cleanNumber . '@s.whatsapp.net';
                $chatId = $waId;
            }

            if ($request->group_wa_id) {
                $chatId = $request->group_wa_id;
            }

            // Handle scheduled sending
            $scheduledAt = null;
            if ($request->scheduled_at) {
                $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                $scheduledAt = Carbon::parse($request->scheduled_at, $userTimezone)->utc();
            }

            // Prepare message payload for custom link preview
            $messageData = json_encode([
                'type' => 'custom',
                'text' => $request->text,
                'previewUrl' => $request->preview_url,
                'previewTitle' => $request->preview_title ?? null,
                'previewDescription' => $request->preview_description ?? null,
                'previewImageUrl' => $request->preview_image_url ?? null,
            ]);

            // Create message record with 'pending' status
            $message = Message::create([
                'waha_session_id' => $sessionRecord->id,
                'template_id' => null,
                'wa_id' => $request->group_wa_id ? null : $waId,
                'group_wa_id' => $request->group_wa_id ?? null,
                'received_number' => $request->phone_number,
                'message' => $messageData,
                'status' => 'pending',
                'scheduled_at' => $scheduledAt,
                'created_by' => Auth::id(),
            ]);

            // Dispatch job to send message asynchronously
            $job = SendMessageJob::dispatch($message->id, $chatId, $wahaSessionName, 'custom')
                ->onQueue('messages');

            // Schedule for later if provided
            if ($scheduledAt) {
                $job->delay($scheduledAt);
            }

            // Log activity
            activity()
                ->performedOn($message)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'recipient' => $chatId,
                        'recipient_type' => $request->group_wa_id ? 'group' : 'contact',
                        'message_type' => 'direct',
                        'session_id' => $sessionRecord->id,
                        'status' => 'pending',
                        'content_type' => 'custom_link',
                    ],
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('queued a custom link message via API');

            DB::commit();

            Log::info('Custom link message queued for sending via API', [
                'message_id' => $message->id,
                'recipient' => $chatId,
                'session' => $wahaSessionName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom link message has been queued for sending.',
                'data' => [
                    'id' => $message->id,
                    'status' => $message->status,
                    'scheduled_at' => $message->scheduled_at?->toIso8601String(),
                    'created_at' => $message->created_at->toIso8601String(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to queue custom link message via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue custom link message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a template message with variable replacement via API.
     *
     * @param SendTemplateMessageRequest $request
     * @return JsonResponse
     */
    public function sendTemplate(SendTemplateMessageRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Get the WAHA session
            $sessionRecord = Session::where('created_by', Auth::id())
                ->where('session_id', $request->session_id)
                ->first();

            if (!$sessionRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found or you do not have access to this session.',
                ], 404);
            }

            $wahaSessionName = $sessionRecord->session_id;

            if (!$wahaSessionName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session configuration.',
                ], 400);
            }

            // Get template (must belong to user) by name
            $template = Template::where('created_by', Auth::id())
                ->where('name', $request->template_name)
                ->first();
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found or you do not have access to this template.',
                ], 404);
            }

            // Clean and format phone number
            $waId = null;
            $chatId = null;

            if ($request->phone_number) {
                $cleanNumber = preg_replace('/[^\d+]/', '', $request->phone_number);
                $cleanNumber = ltrim($cleanNumber, '+');
                $waId = $cleanNumber . '@s.whatsapp.net';
                $chatId = $waId;
            }

            if ($request->group_wa_id) {
                $chatId = $request->group_wa_id;
            }

            // Handle scheduled sending
            $scheduledAt = null;
            if ($request->scheduled_at) {
                $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');
                $scheduledAt = Carbon::parse($request->scheduled_at, $userTimezone)->utc();
            }

            // Build message content from template using ordered placeholders
            $header = $template->header ?? '';
            $body = $template->body ?? '';

            $headerParams = [];
            if ($header) {
                preg_match_all('/\{\{(\w+)\}\}/', $header, $matches);
                $headerParams = array_values(array_unique($matches[1]));
            }

            $bodyParams = [];
            if ($body) {
                preg_match_all('/\{\{(\w+)\}\}/', $body, $matches);
                $bodyParams = array_values(array_unique($matches[1]));
            }

            $placeholderHeaders = $request->placeholder_headers ?? [];
            $placeholders = $request->placeholders ?? [];

            foreach ($headerParams as $index => $param) {
                $value = $placeholderHeaders[$index] ?? '';
                $header = str_replace('{{' . $param . '}}', $value, $header);
            }

            foreach ($bodyParams as $index => $param) {
                $value = $placeholders[$index] ?? '';
                $body = str_replace('{{' . $param . '}}', $value, $body);
            }

            $content = '';
            if ($header) {
                $content .= '*' . $header . "*\n\n";
            }
            $content .= $body;
            $content = trim($content);

            // Create message record with 'pending' status
            $message = Message::create([
                'waha_session_id' => $sessionRecord->id,
                'template_id' => $template->id,
                'wa_id' => $request->group_wa_id ? null : $waId,
                'group_wa_id' => $request->group_wa_id ?? null,
                'received_number' => $request->phone_number,
                'message' => $content,
                'status' => 'pending',
                'scheduled_at' => $scheduledAt,
                'created_by' => Auth::id(),
            ]);

            // Dispatch job to send message asynchronously
            $job = SendMessageJob::dispatch($message->id, $chatId, $wahaSessionName, 'text')
                ->onQueue('messages');

            // Schedule for later if provided
            if ($scheduledAt) {
                $job->delay($scheduledAt);
            }

            // Update template usage stats
            $template->incrementUsageCount();
            $template->update(['last_used_at' => now()]);

            // Log activity
            activity()
                ->performedOn($message)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'recipient' => $chatId,
                        'recipient_type' => $request->group_wa_id ? 'group' : 'contact',
                        'message_type' => 'template',
                        'session_id' => $sessionRecord->id,
                        'template_id' => $template->id,
                        'status' => 'pending',
                    ],
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('queued a template message via API');

            DB::commit();

            Log::info('Template message queued for sending via API', [
                'message_id' => $message->id,
                'recipient' => $chatId,
                'session' => $wahaSessionName,
                'template_id' => $template->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template message has been queued for sending.',
                'data' => [
                    'id' => $message->id,
                    'status' => $message->status,
                    'scheduled_at' => $message->scheduled_at?->toIso8601String(),
                    'created_at' => $message->created_at->toIso8601String(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to queue template message via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue template message: ' . $e->getMessage(),
            ], 500);
        }
    }
}

