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
            // Get the selected session - only sessions created by current user
            $session = Session::where('created_by', Auth::id())->find($this->selectedSessionId);

            if (!$session) {
                throw new \Exception('Selected session does not exist or you do not have permission to sync groups from this session.');
            }

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
            $deletedCount = 0;

            DB::transaction(function () use ($groupsData, $session, &$syncedCount, &$deletedCount) {
                // Delete all existing groups for this session first
                $deletedCount = Group::where('waha_session_id', $session->id)->delete();

                // Create new groups from API data
                foreach ($groupsData as $groupId => $groupData) {
                    // Prepare group data for creation
                    $groupAttributes = [
                        'waha_session_id' => $session->id,
                        'group_wa_id' => $groupId,
                        'name' => $groupData['subject'] ?? null,
                        'detail' => $groupData,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Create new group
                    Group::create($groupAttributes);

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
                    'groups_deleted' => $deletedCount,
                    'groups_synced' => $syncedCount,
                    'api_response_status' => $response->status(),
                ])
                ->log('synchronized groups from session');

            session()->flash('success', "Successfully synchronized {$syncedCount} groups from {$session->name}. {$deletedCount} old groups were removed.");
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
            ->forUser(Auth::id()) // Only show groups from sessions created by current user
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

        // Get active sessions for filtering and syncing - only sessions created by current user
        $syncableSessions = Session::where('created_by', Auth::id())->active()->orderBy('name')->get();
        $availableSessions = Session::where('created_by', Auth::id())->orderBy('name')->get();

        return view('livewire.groups.groups-index', compact('groups', 'syncableSessions', 'availableSessions'));
    }
}
