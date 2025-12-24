<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class SendFileMessageRequest extends FormRequest
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
            'file_url' => [
                'required',
                'url',
                'max:2048',
            ],
            'caption' => [
                'nullable',
                'string',
                'max:1024',
            ],
            'mimetype' => [
                'nullable',
                'string',
                'max:255',
            ],
            'filename' => [
                'nullable',
                'string',
                'max:255',
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
            'file_url.required' => 'File URL is required.',
            'file_url.url' => 'File URL must be a valid URL.',
            'caption.max' => 'Caption must not exceed 1024 characters.',
        ];
    }
}

