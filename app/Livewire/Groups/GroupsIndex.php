<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\Session;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

#[Title('Groups')]
class GroupsIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $queryString = [
        'search' => ['except' => ''],
        'sessionFilter' => ['except' => ''],
        'communityFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public $search = '';
    public $sessionFilter = '';
    public $communityFilter = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public $selectedSessionId;

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
            'sessionFilter',
            'communityFilter'
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

    public function syncGroups()
    {
        $this->authorize('group.sync');

        $this->validate([
            'selectedSessionId' => 'required|exists:waha_sessions,id',
        ], [
            'selectedSessionId.required' => 'Please select a session to sync groups from.',
            'selectedSessionId.exists' => 'Selected session does not exist.',
        ]);

        try {
            // Get the selected session
            $session = Session::find($this->selectedSessionId);

            // Call WAHA API to get groups
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => env('WAHA_API_KEY'),
            ])->get(env('WAHA_API_URL') . '/api/' . $session->session_id . '/groups');

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch groups from WAHA API: ' . ' ' . $response->status() . ' ' . $response->body());
            }

            $groupsData = $response->json();
            $syncedCount = 0;

            DB::transaction(function () use ($groupsData, $session, &$syncedCount) {
                foreach ($groupsData as $groupId => $groupData) {
                    // Prepare group data for upsert
                    $groupAttributes = [
                        'waha_session_id' => $session->id,
                        'group_wa_id' => $groupId,
                        'name' => $groupData['subject'] ?? null,
                        'detail' => $groupData,
                        'updated_at' => now(),
                    ];

                    // Upsert group (insert if not exists, update if exists)
                    Group::updateOrCreate(
                        [
                            'waha_session_id' => $session->id,
                            'group_wa_id' => $groupId,
                        ],
                        $groupAttributes
                    );

                    $syncedCount++;
                }
            });

            // Log the sync activity
            activity()
                ->performedOn($session)
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'waha_session_id' => $this->selectedSessionId,
                    'session_name' => $session->name,
                    'sync_type' => 'groups_sync',
                    'groups_synced' => $syncedCount,
                    'api_response_status' => $response->status(),
                ])
                ->log('synchronized groups from session');

            session()->flash('success', "Successfully synchronized {$syncedCount} groups from {$session->name}.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to synchronize groups: ' . $e->getMessage());
        }

        // Reset selected session and close modal regardless of success/failure
        $this->selectedSessionId = null;
        $this->modal('sync-groups-modal')->close();
    }

    public function render()
    {
        $groups = Group::with(['wahaSession'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('group_wa_id', 'like', '%' . $this->search . '%')
                          ->orWhereRaw("JSON_EXTRACT(detail, '$.desc') LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->when($this->sessionFilter, fn($q) => $q->where('waha_session_id', $this->sessionFilter))
            ->when($this->communityFilter === 'community', fn($q) => $q->where('detail->isCommunity', true))
            ->when($this->communityFilter === 'group', fn($q) => $q->where('detail->isCommunity', false))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Get active sessions for filtering and syncing
        $syncableSessions = Session::active()->orderBy('name')->get();
        $availableSessions = Session::orderBy('name')->get();

        return view('livewire.groups.groups-index', compact('groups', 'syncableSessions', 'availableSessions'));
    }
}
