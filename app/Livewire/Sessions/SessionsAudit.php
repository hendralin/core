<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use Livewire\Component;
use App\Models\Activity;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

#[Title('Session Audit Trail')]
class SessionsAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedSession = null;
    public $sessions = [];

    public function mount()
    {
        $this->authorize('session.view');
        $this->sessions = Session::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedSession'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedSession()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedSession']);
        $this->resetPage();
    }

    public function render()
    {
        // Get sessions data from WAHA API with caching
        $cacheKey = 'waha_sessions_data';
        $apiSessions = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            try {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'X-Api-Key' => env('WAHA_API_KEY'),
                ])->get(env('WAHA_API_URL') . "/api/sessions?all=true");

                if ($response->successful()) {
                    return $response->json();
                }
                return null;
            } catch (\Exception $e) {
                Log::warning('Failed to fetch sessions from WAHA API: ' . $e->getMessage());
                return null;
            }
        });

        // Get database sessions and merge with API data for filter dropdown
        $dbSessions = Session::all()->keyBy('session_id');
        $sessions = collect();

        if ($apiSessions) {
            foreach ($apiSessions as $apiSession) {
                // Filter: only show sessions that exist in database
                if (isset($dbSessions[$apiSession['name']])) {
                    $dbSession = $dbSessions[$apiSession['name']];

                    // Merge API data with database data
                    $sessionData = [
                        'id' => $dbSession->id,
                        'name' => $dbSession->name,
                        'session_id' => $apiSession['name'],
                        'status' => $apiSession['status'] ?? 'UNKNOWN',
                        'created_at' => $dbSession->created_at,
                        'me' => $apiSession['me'] ?? null,
                        'presence' => $apiSession['presence'] ?? null,
                    ];

                    $sessions->push((object) $sessionData);
                }
            }
        } else {
            // Fallback: show all database sessions with unknown status
            foreach ($dbSessions as $dbSession) {
                $sessionData = [
                    'id' => $dbSession->id,
                    'name' => $dbSession->name,
                    'session_id' => $dbSession->session_id,
                    'status' => 'UNKNOWN',
                    'created_at' => $dbSession->created_at,
                    'me' => null,
                    'presence' => null,
                ];
                $sessions->push((object) $sessionData);
            }
        }

        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Session::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhere('event', 'like', '%' . $this->search . '%')
                      ->orWhereHas('causer', function ($causerQuery) {
                          $causerQuery->where('name', 'like', '%' . $this->search . '%')
                                    ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->selectedSession, function ($query) {
                $query->where('subject_id', $this->selectedSession);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.sessions.sessions-audit', compact('activities', 'sessions', 'apiSessions'));
    }
}
