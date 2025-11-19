<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

#[Title('Session Details')]
class SessionsShow extends Component
{
    public Session $session;
    public $sessionData = null;
    public $qrCodeImage = null;

    public function mount(Session $session)
    {
        $this->authorize('session.view');
        $this->session = $session;
        $this->fetchSessionData();
    }

    public function fetchSessionData()
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => env('WAHA_API_KEY'),
            ])->get(env('WAHA_API_URL') . "/api/sessions/{$this->session->session_id}");

            if ($response->successful()) {
                $this->sessionData = $response->json();
            } else {
                Log::warning('Failed to fetch session data from WAHA API', [
                    'session_id' => $this->session->session_id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                $this->sessionData = null;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching session data from WAHA API: ' . $e->getMessage(), [
                'session_id' => $this->session->session_id,
            ]);
            $this->sessionData = null;
        }
    }

    public function refreshSessionData()
    {
        $this->fetchSessionData();
    }

    public function startSession()
    {
        $this->authorize('session.connect');

        try {
            // Hit WAHA API to start session
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => env('WAHA_API_KEY'),
            ])->post(env('WAHA_API_URL') . "/api/sessions/{$this->session->session_id}/start");

            if ($response->successful()) {
                // Log activity
                activity()
                    ->performedOn($this->session)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => Request::ip(),
                        'user_agent' => Request::userAgent(),
                        'api_response' => $response->json(),
                    ])
                    ->log('started WAHA session via API');

                $this->refreshSessionData();
                session()->flash('success', 'Session started successfully.');
            } else {
                Log::warning('Failed to start session via WAHA API', [
                    'session_id' => $this->session->session_id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                session()->flash('error', 'Failed to start session: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('startSession error: ' . $e->getMessage(), [
                'session_id' => $this->session->session_id,
            ]);
            session()->flash('error', 'Failed to start session: ' . $e->getMessage());
        }
    }

    public function restartSession()
    {
        $this->authorize('session.connect');

        try {
            // Hit WAHA API to restart session
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => env('WAHA_API_KEY'),
            ])->post(env('WAHA_API_URL') . "/api/sessions/{$this->session->session_id}/restart");

            if ($response->successful()) {
                // Log activity
                activity()
                    ->performedOn($this->session)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => Request::ip(),
                        'user_agent' => Request::userAgent(),
                        'api_response' => $response->json(),
                    ])
                    ->log('restarted WAHA session via API');

                $this->refreshSessionData();
                session()->flash('success', 'Session restarted successfully.');
            } else {
                Log::warning('Failed to restart session via WAHA API', [
                    'session_id' => $this->session->session_id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                session()->flash('error', 'Failed to restart session: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('restartSession error: ' . $e->getMessage(), [
                'session_id' => $this->session->session_id,
            ]);
            session()->flash('error', 'Failed to restart session: ' . $e->getMessage());
        }
    }

    public function stopSession()
    {
        $this->authorize('session.disconnect');

        try {
            // Hit WAHA API to stop session
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => env('WAHA_API_KEY'),
            ])->post(env('WAHA_API_URL') . "/api/sessions/{$this->session->session_id}/stop");

            if ($response->successful()) {
                // Log activity
                activity()
                    ->performedOn($this->session)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => Request::ip(),
                        'user_agent' => Request::userAgent(),
                        'api_response' => $response->json(),
                    ])
                    ->log('stopped WAHA session via API');

                $this->refreshSessionData();
                session()->flash('success', 'Session stopped successfully.');
            } else {
                Log::warning('Failed to stop session via WAHA API', [
                    'session_id' => $this->session->session_id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                session()->flash('error', 'Failed to stop session: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('stopSession error: ' . $e->getMessage(), [
                'session_id' => $this->session->session_id,
            ]);
            session()->flash('error', 'Failed to stop session: ' . $e->getMessage());
        }
    }

    public function scanQRCode()
    {
        $this->authorize('session.connect');

        try {
            // Hit WAHA API to get QR code screenshot
            $response = Http::withHeaders([
                'accept' => 'image/jpeg',
                'X-Api-Key' => env('WAHA_API_KEY'),
            ])->get(env('WAHA_API_URL') . "/api/screenshot?session={$this->session->session_id}");

            if ($response->successful()) {
                // Convert response to base64 for display
                $this->qrCodeImage = 'data:image/jpeg;base64,' . base64_encode($response->body());

                // Log activity
                activity()
                    ->performedOn($this->session)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => Request::ip(),
                        'user_agent' => Request::userAgent(),
                    ])
                    ->log('generated QR code for WAHA session');

                session()->flash('success', 'QR Code generated successfully.');

                // Open QR code modal
                $this->modal('qr-code-modal')->show();
            } else {
                Log::warning('Failed to generate QR code via WAHA API', [
                    'session_id' => $this->session->session_id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                session()->flash('error', 'Failed to generate QR code: ' . $response->body());
                $this->qrCodeImage = null;
            }
        } catch (\Exception $e) {
            Log::error('scanQRCode error: ' . $e->getMessage(), [
                'session_id' => $this->session->session_id,
            ]);
            session()->flash('error', 'Failed to generate QR code: ' . $e->getMessage());
            $this->qrCodeImage = null;
        }
    }

    public function delete()
    {
        $this->authorize('session.delete');

        try {
            // First, delete from WAHA API
            try {
                $apiResponse = Http::withHeaders([
                    'accept' => '*/*',
                    'X-Api-Key' => env('WAHA_API_KEY'),
                ])->delete(env('WAHA_API_URL') . "/api/sessions/{$this->session->session_id}");

                if (!$apiResponse->successful()) {
                    Log::error('Failed to delete session from WAHA API: ' . $apiResponse->body());
                    session()->flash('error', 'Failed to delete session from WAHA API.');
                    return;
                }
            } catch (\Exception $e) {
                Log::error('Exception while deleting session from WAHA API: ' . $e->getMessage());
                session()->flash('error', 'Failed to delete session from WAHA API: ' . $e->getMessage());
                return;
            }

            DB::transaction(function () {
                // Log activity before deletion
                activity()
                    ->performedOn($this->session)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'ip' => Request::ip(),
                        'user_agent' => Request::userAgent(),
                        'deleted_session_data' => [
                            'name' => $this->session->name,
                            'session_id' => $this->session->session_id,
                        ]
                    ])
                    ->log('deleted WAHA session');

                $this->session->delete();
            });

            session()->flash('success', 'Session deleted from database and WAHA API.');

            return $this->redirect('/sessions', true);
        } catch (\Throwable $e) {
            if ($e->errorInfo[0] == 23000) {
                session()->flash('error', "The session cannot be deleted because it is already in use.");
            } else {
                session()->flash('error', $e->getMessage());
            }
        }
    }

    public function render()
    {
        return view('livewire.sessions.sessions-show');
    }
}
