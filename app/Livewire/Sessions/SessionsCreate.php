<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

#[Title('Create Session')]
class SessionsCreate extends Component
{
    public $name;
    public $session_id;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255|unique:waha_sessions,name',
        'session_id' => 'required|string|max:255|regex:/^[a-zA-Z0-9_-]+$/|unique:waha_sessions,session_id',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'session_id.regex' => 'Session ID can only contain alphanumeric characters, hyphens, and underscores (a-z, A-Z, 0-9, -, _)',
    ];

    public function updatedName()
    {
        $this->validateOnly('name');
    }

    public function updatedSessionId()
    {
        $this->validateOnly('session_id');
    }

    public function save()
    {
        $this->authorize('session.create');

        $validatedData = $this->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                $session = Session::create([
                    ...$validatedData,
                    'created_by' => Auth::id(),
                ]);

                // Log activity
                activity()
                    ->performedOn($session)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => Request::ip(),
                        'user_agent' => Request::userAgent(),
                        'created_session_data' => [
                            'name' => $session->name,
                            'session_id' => $session->session_id,
                        ]
                    ])
                    ->log('created WAHA session');
            });

            // Call WAHA API to create session
            try {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'X-Api-Key' => env('WAHA_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->post(env('WAHA_API_URL') . '/api/sessions', [
                    'name' => $validatedData['session_id'],
                    'config' => [
                        'metadata' => (object)[],
                        'webhooks' => [],
                        'noweb' => [
                            'markOnline' => true,
                            'store' => [
                                'enabled' => true,
                                'fullSync' => true
                            ]
                        ]
                    ],
                    'start' => false
                ]);

                if ($response->status() !== 201) {
                    Log::warning('WAHA session creation returned status: ' . $response->status() . ' - ' . $response->body());
                }
            } catch (\Exception $apiException) {
                Log::error('WAHA API call failed: ' . $apiException->getMessage());
                // Continue with success since database session was created
            }

            // Clear sessions cache to refresh data on index page
            Cache::forget('waha_sessions_data');

            session()->flash('success', 'Session created successfully.');
            return $this->redirect('/sessions', true);
        } catch (\Exception $e) {
            Log::error('Session creation error: ' . $e->getMessage());
            session()->flash('error', 'Failed to create session: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sessions.sessions-create');
    }
}
