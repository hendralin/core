<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use App\Models\Session;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

#[Title('Contacts')]
class ContactsIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $queryString = [
        'search' => ['except' => ''],
        'sessionFilter' => ['except' => ''],
        'verifiedFilter' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public $search = '';
    public $sessionFilter = '';
    public $verifiedFilter = '';
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
            'verifiedFilter'
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

    public function syncContacts()
    {
        $this->authorize('contact.sync');

        $this->validate([
            'selectedSessionId' => 'required|exists:waha_sessions,id',
        ], [
            'selectedSessionId.required' => 'Please select a session to sync contacts from.',
            'selectedSessionId.exists' => 'Selected session does not exist.',
        ]);

        try {
            // Get the selected session - only sessions created by current user
            $session = Session::where('created_by', Auth::id())->find($this->selectedSessionId);

            if (!$session) {
                throw new \Exception('Selected session does not exist or you do not have permission to sync contacts from this session.');
            }

            // Call WAHA API to get contacts
            $response = Http::withHeaders([
                'accept' => '*/*',
                'X-Api-Key' => env('WAHA_API_KEY'),
            ])->get(env('WAHA_API_URL') . '/api/contacts/all', [
                'session' => $session->session_id, // Use session_id field from database
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch contacts from WAHA API: ' . ' ' . $response->status() . ' ' . $response->body());
            }

            $contactsData = $response->json();
            $syncedCount = 0;
            $deletedCount = 0;

            DB::transaction(function () use ($contactsData, $session, &$syncedCount, &$deletedCount) {
                // Delete all existing contacts for this session first
                $deletedCount = Contact::where('waha_session_id', $session->id)->delete();

                // Create new contacts from API data
                foreach ($contactsData as $contactData) {
                    // Skip group chats (IDs ending with @g.us) and LID contacts (IDs ending with @lid)
                    if (str_ends_with($contactData['id'], '@g.us') || str_ends_with($contactData['id'], '@lid')) {
                        continue;
                    }

                    // Prepare contact data for creation
                    $contactAttributes = [
                        'waha_session_id' => $session->id,
                        'wa_id' => $contactData['id'],
                        'name' => $contactData['name'] ?? null,
                        'verified_name' => $contactData['verifiedName'] ?? null,
                        'push_name' => $contactData['pushname'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Create new contact
                    Contact::create($contactAttributes);

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
                    'sync_type' => 'contacts_sync',
                    'contacts_deleted' => $deletedCount,
                    'contacts_synced' => $syncedCount,
                    'api_response_status' => $response->status(),
                ])
                ->log('synchronized contacts from session');

            session()->flash('success', "Successfully synchronized {$syncedCount} contacts from {$session->name}. {$deletedCount} old contacts were removed.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to synchronize contacts: ' . $e->getMessage());
        }

        // Reset selected session and close modal regardless of success/failure
        $this->selectedSessionId = null;
        $this->modal('sync-contacts-modal')->close();
    }

    public function render()
    {
        $contacts = Contact::with(['wahaSession'])
            ->forUser(Auth::id()) // Only show contacts from sessions created by current user
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('wa_id', 'like', '%' . $this->search . '%')
                          ->orWhere('verified_name', 'like', '%' . $this->search . '%')
                          ->orWhere('push_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->sessionFilter, fn($q) => $q->where('waha_session_id', $this->sessionFilter))
            ->when($this->verifiedFilter === 'verified', fn($q) => $q->whereNotNull('verified_name'))
            ->when($this->verifiedFilter === 'unverified', fn($q) => $q->whereNull('verified_name'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Get active sessions for filtering and syncing - only sessions created by current user
        $syncableSessions = Session::where('created_by', Auth::id())->active()->orderBy('name')->get();
        $availableSessions = Session::where('created_by', Auth::id())->orderBy('name')->get();

        return view('livewire.contacts.contacts-index', compact('contacts', 'syncableSessions', 'availableSessions'));
    }
}
