<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class SendCustomLinkMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware handles auth
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'session_id' => [
                'required',
                'string',
                Rule::exists('waha_sessions', 'session_id')->where(function ($query) {
                    return $query->where('created_by', Auth::id());
                }),
            ],
            'phone_number' => [
                'required_without:group_wa_id',
                'regex:/^(\+?\d{1,3}[-.\s]?)?\(?\d{1,4}\)?[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/',
            ],
            'group_wa_id' => [
                'required_without:phone_number',
                'string',
            ],
            'text' => [
                'required',
                'string',
                'max:1024',
            ],
            'preview_url' => [
                'required',
                'url',
                'max:2048',
            ],
            'preview_title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'preview_description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'preview_image_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
            'scheduled_at' => [
                'nullable',
                'date',
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation: URL in Message Text must be identical to Preview URL
            if ($this->text && $this->preview_url) {
                // Extract URL from message text (simple pattern - stops at whitespace)
                preg_match('/https?:\/\/[^\s]+/i', $this->text, $matches);

                if (empty($matches) || empty($matches[0])) {
                    $validator->errors()->add('text', 'Message Text must contain a URL that is identical to the Preview URL.');
                    return;
                }

                $extractedUrl = trim($matches[0]);
                $previewUrl = trim($this->preview_url);

                // Compare URLs directly - must be exactly the same
                if ($extractedUrl !== $previewUrl) {
                    $validator->errors()->add('text', 'The URL in Message Text must be identical to the Preview URL.');
                    $validator->errors()->add('preview_url', 'The Preview URL must match the URL in Message Text exactly.');
                }
            }

            // Validate scheduled_at with timezone
            if ($this->scheduled_at) {
                try {
                    // Get user's timezone, fallback to app timezone
                    $userTimezone = Auth::user()->timezone ?? config('app.timezone', 'UTC');

                    // Parse selected time in user's timezone
                    $selectedTime = Carbon::parse($this->scheduled_at, $userTimezone);

                    // Get current time in user's timezone and add 5 minutes
                    $minTime = now($userTimezone)->addMinutes(5);

                    if ($selectedTime->lte($minTime)) {
                        $validator->errors()->add('scheduled_at', 'Schedule time must be at least 5 minutes in the future.');
                    }
                } catch (\Exception $e) {
                    $validator->errors()->add('scheduled_at', 'Invalid date/time format. Please select a valid time.');
                }
            }
        });
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'session_id.required' => 'Session ID is required.',
            'session_id.exists' => 'Session not found or you do not have access to this session.',
            'phone_number.required_without' => 'Phone number is required when group_wa_id is not provided.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            'group_wa_id.required_without' => 'Group WA ID is required when phone_number is not provided.',
            'text.required' => 'Message text is required.',
            'text.max' => 'Message text must not exceed 1024 characters.',
            'preview_url.required' => 'Preview URL is required.',
            'preview_url.url' => 'Preview URL must be a valid URL.',
            'preview_title.max' => 'Preview title must not exceed 255 characters.',
            'preview_description.max' => 'Preview description must not exceed 500 characters.',
            'preview_image_url.url' => 'Preview image must be a valid URL.',
        ];
    }
}

