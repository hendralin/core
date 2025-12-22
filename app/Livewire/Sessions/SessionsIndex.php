<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use App\Traits\HasWahaConfig;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

#[Title('Sessions')]
class SessionsIndex extends Component
{
    use WithPagination, WithoutUrlPagination, HasWahaConfig;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public $sessionIdToDelete = null;

    public function updatedStatusFilter()
    {
        $this->clearPage();
    }

    public function updatingSearch()
    {
        $this->clearPage();
    }

    public function updatingPerPage()
    {
        $this->clearPage();
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
    }
    public function clearPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'statusFilter'
        ]);

        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function setSessionToDelete($sessionId)
    {
        $this->sessionIdToDelete = $sessionId;
    }

    public function delete($id = null)
    {
        $this->authorize('session.delete');

        try {
            $sessionId = $id ?: $this->sessionIdToDelete;

            if (!$sessionId) {
                session()->flash('error', 'No session selected for deletion.');
                return;
            }
            // Session exists in database
            $session = Session::where('created_by', Auth::id())->find($sessionId);

            if ($session) {
                // First, delete from WAHA API
                try {
                    $apiUrl = $this->getWahaApiUrl();
                    $apiKey = $this->getWahaApiKey();
                    
                    if (!$apiUrl || !$apiKey) {
                        throw new \Exception('WAHA configuration not found. Please configure your WAHA settings first.');
                    }

                    $apiResponse = Http::withHeaders([
                        'accept' => '*/*',
                        'X-Api-Key' => $apiKey,
                    ])->delete($apiUrl . "/api/sessions/{$session->session_id}");

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
                DB::transaction(function () use ($session) {
                    // Log activity before deletion
                    activity()
                        ->performedOn($session)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'ip' => Request::ip(),
                            'user_agent' => Request::userAgent(),
                            'deleted_session_data' => [
                                'name' => $session->name,
                                'session_id' => $session->session_id,
                            ]
                        ])
                        ->log('deleted WAHA session');

                    $session->delete();
                });

                session()->flash('success', 'Session deleted from database and WAHA API.');
                $this->sessionIdToDelete = null;

                // Close the modal
                $this->modal('delete-session')->close();
            } else {
                // Session only exists in API, cannot delete from database
                session()->flash('error', 'Cannot delete session that is not registered in the database.');
                return;
            }
        } catch (\Throwable $e) {
            if ($e instanceof \PDOException && isset($e->errorInfo[0]) && $e->errorInfo[0] == 23000) {
                session()->flash('error', "The session cannot be deleted because it is already in use.");
            } else {
                session()->flash('error', $e->getMessage());
            }
        }
    }

    public function clearCache()
    {
        try {
            // Clear sessions cache
            Cache::forget('waha_sessions_data');

            // Note: Individual profile picture caches will expire naturally (15 minutes)
            // For immediate refresh, they will be re-fetched on next page load

            session()->flash('success', 'Cache cleared successfully. Data will be refreshed on next load.');
            Log::info('WAHA sessions cache cleared by user');
        } catch (\Exception $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage());
            session()->flash('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Check if WAHA is configured
        if (!$this->isWahaConfigured()) {
            return view('livewire.sessions.sessions-index', [
                'sessions' => collect(),
                'wahaConfigured' => false
            ]);
        }

        $sessions = collect();

        try {
            // Cache sessions data for 5 minutes to reduce API calls
            $apiUrl = $this->getWahaApiUrl();
            $apiKey = $this->getWahaApiKey();
            $cacheKey = 'waha_sessions_data_' . Auth::id();
            $apiSessions = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($apiUrl, $apiKey) {
                try {
                    $response = Http::withHeaders([
                        'accept' => 'application/json',
                        'X-Api-Key' => $apiKey,
                    ])->get($apiUrl . "/api/sessions?all=true");

                    if ($response->successful()) {
                        return $response->json();
                    }
                    return null;
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch sessions from WAHA API: ' . $e->getMessage());
                    return null;
                }
            });

            if ($apiSessions) {

                // Get all database sessions for filtering (by session_id) - only sessions created by current user
                $dbSessions = Session::where('created_by', Auth::id())->get()->keyBy('session_id');

                foreach ($apiSessions as $apiSession) {
                    // Filter: only show sessions that exist in database (by session_id)
                    if (isset($dbSessions[$apiSession['name']])) {
                        $dbSession = $dbSessions[$apiSession['name']];

                        // Get profile picture with caching (15 minutes - profile pictures don't change often)
                        $profilePicture = null;
                        if (isset($apiSession['me']['id'])) {
                            $profileCacheKey = 'profile_picture_' . $apiSession['me']['id'];
                            $profilePicture = Cache::remember($profileCacheKey, now()->addMinutes(15), function () use ($apiSession) {
                                try {
                                    $contactId = urlencode($apiSession['me']['id']);
                                    $sessionName = $apiSession['name'];

                                    $profileResponse = Http::withHeaders([
                                        'accept' => '*/*',
                                        'X-Api-Key' => $this->getWahaApiKey(),
                                    ])->get($this->getWahaApiUrl() . "/api/contacts/profile-picture?contactId={$contactId}&refresh=false&session={$sessionName}");

                                    if ($profileResponse->successful()) {
                                        // API returns JSON with profilePictureURL field
                                        $profileData = $profileResponse->json();
                                        return $profileData['profilePictureURL'] ?? null;
                                    }
                                    return null;
                                } catch (\Exception $e) {
                                    Log::warning('Failed to get profile picture for session ' . $apiSession['name'] . ': ' . $e->getMessage());
                                    return null;
                                }
                            });
                        }
                        // Merge API data with database data
                        $sessionData = [
                            'id' => $dbSession->id,
                            'name' => $dbSession->name,
                            'session_id' => $apiSession['name'], // API uses 'name' as session identifier
                            'status' => $apiSession['status'] ?? 'UNKNOWN',
                            'created_at' => $dbSession->created_at,
                            'updated_at' => $dbSession->updated_at,
                            'config' => $apiSession['config'] ?? null,
                            'me' => $apiSession['me'] ?? null,
                            'engine' => $apiSession['engine'] ?? null,
                            'assignedWorker' => $apiSession['assignedWorker'] ?? null,
                            'profile_picture' => $profilePicture,
                        ];

                        $sessions->push((object) $sessionData);
                    }
                    // Skip sessions that don't exist in database
                }
            } else {
                Log::error('Failed to fetch sessions from WAHA API (cached or fresh)');
                // Fallback: show all database sessions with unknown status - only sessions created by current user
                $dbSessions = Session::where('created_by', Auth::id())->get();
                foreach ($dbSessions as $dbSession) {
                    $sessionData = [
                        'id' => $dbSession->id,
                        'name' => $dbSession->name,
                        'session_id' => $dbSession->session_id,
                        'status' => 'UNKNOWN',
                        'created_at' => $dbSession->created_at,
                        'updated_at' => $dbSession->updated_at,
                        'config' => null,
                        'me' => null,
                        'engine' => null,
                        'assignedWorker' => null,
                        'profile_picture' => null,
                    ];
                    $sessions->push((object) $sessionData);
                }
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching sessions: ' . $e->getMessage());
            // Fallback: show all database sessions with error status - only sessions created by current user
            $dbSessions = Session::where('created_by', Auth::id())->get();
            foreach ($dbSessions as $dbSession) {
                $sessionData = [
                    'id' => $dbSession->id,
                    'name' => $dbSession->name,
                    'session_id' => $dbSession->session_id,
                    'status' => 'ERROR',
                    'created_at' => $dbSession->created_at,
                    'updated_at' => $dbSession->updated_at,
                    'config' => null,
                    'me' => null,
                    'engine' => null,
                    'assignedWorker' => null,
                    'profile_picture' => null,
                ];
                $sessions->push((object) $sessionData);
            }
        }
        // Apply filtering
        if ($this->search) {
            $sessions = $sessions->filter(function ($session) {
                return str_contains(strtolower($session->name), strtolower($this->search)) ||
                       str_contains(strtolower($session->session_id), strtolower($this->search));
            });
        }
        if ($this->statusFilter !== '') {
            $sessions = $sessions->filter(function ($session) {
                return $session->status === $this->statusFilter;
            });
        }
        // Apply sorting
        $sessions = $sessions->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'desc');

        // Apply pagination manually
        $total = $sessions->count();
        $page = $this->getPage();
        $perPage = $this->perPage;
        $offset = ($page - 1) * $perPage;

        $paginatedSessions = $sessions->slice($offset, $perPage)->values();

        // Create a LengthAwarePaginator manually
        $sessions = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedSessions,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return view('livewire.sessions.sessions-index', [
            'sessions' => $sessions,
            'wahaConfigured' => true
        ]);
    }
}
