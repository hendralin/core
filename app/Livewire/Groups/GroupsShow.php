<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\Contact;
use App\Traits\HasWahaConfig;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

#[Title('Show Group')]
class GroupsShow extends Component
{
    use HasWahaConfig;
    public Group $group;
    public $groupPictureUrl;
    public $participantPictures = [];
    public $participantNames = [];
    public $previewImageUrl = null;
    public $previewParticipantId = null;

    public function mount(Group $group): void
    {
        $this->group = $group->load(['wahaSession']); // Eager load relationships

        // Check if group belongs to a session created by current user
        if (!$this->group->wahaSession || $this->group->wahaSession->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to view this group.');
        }

        // Check if group picture already exists in database, otherwise fetch from API
        $this->loadOrFetchGroupPicture();

        // Initialize participant pictures and names arrays
        if ($group->detail && isset($group->detail['participants'])) {
            foreach ($group->detail['participants'] as $participant) {
                $this->participantPictures[$participant['id']] = null;
                $this->participantNames[$participant['id']] = null;
            }

            // Load contact names for all participants
            $this->loadParticipantNames();
        }
    }

    private function loadOrFetchGroupPicture()
    {
        // First, check if we already have the group picture URL in database
        if (!empty($this->group->picture_url)) {
            $this->groupPictureUrl = $this->group->picture_url;
            return;
        }

        // If not in database, try to fetch from WAHA API (if available)
        try {
            $apiUrl = $this->getWahaApiUrl();
            $apiKey = $this->getWahaApiKey();
            
            if (!$apiUrl || !$apiKey) {
                $this->groupPictureUrl = null;
                return;
            }

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => $apiKey,
            ])->get($apiUrl . '/api/' . $this->group->wahaSession?->session_id . '/groups/' . $this->group->group_wa_id . '/picture', [
                'refresh' => 'false'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $groupPictureUrl = $data['url'] ?? null;

                // Save to database for future use
                if ($groupPictureUrl) {
                    $this->group->update(['picture_url' => $groupPictureUrl]);
                }

                $this->groupPictureUrl = $groupPictureUrl;
            } else {
                // If API returns error, save null to avoid repeated calls
                $this->group->update(['picture_url' => null]);
                $this->groupPictureUrl = null;
            }
        } catch (\Exception $e) {
            // Silently fail if API call fails, save null to database to avoid repeated calls
            $this->group->update(['picture_url' => null]);
            $this->groupPictureUrl = null;
        }
    }

    public function loadParticipantPicture($participantId)
    {
        // Skip if already loaded
        if (isset($this->participantPictures[$participantId]) && $this->participantPictures[$participantId] !== null) {
            return;
        }

        try {
            $apiUrl = $this->getWahaApiUrl();
            $apiKey = $this->getWahaApiKey();
            
            if (!$apiUrl || !$apiKey) {
                $this->participantPictures[$participantId] = null;
                return;
            }

            $response = Http::withHeaders([
                'accept' => '*/*',
                'X-Api-Key' => $apiKey,
            ])->get($apiUrl . '/api/contacts/profile-picture', [
                'contactId' => $participantId,
                'refresh' => 'false',
                'session' => $this->group->wahaSession?->session_id
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->participantPictures[$participantId] = $data['profilePictureURL'] ?? null;
            } else {
                $this->participantPictures[$participantId] = null;
            }
        } catch (\Exception $e) {
            $this->participantPictures[$participantId] = null;
        }
    }

    private function loadParticipantNames()
    {
        if (!$this->group->detail || !isset($this->group->detail['participants'])) {
            return;
        }

        // Collect all participant IDs and phone numbers
        $participantIds = [];
        $phoneNumbers = [];

        foreach ($this->group->detail['participants'] as $participant) {
            if (isset($participant['id'])) {
                $participantIds[] = $participant['id'];
            }
            if (isset($participant['phoneNumber'])) {
                $phoneNumbers[] = $participant['phoneNumber'];
            }
        }

        // Combine all possible IDs to search for
        $searchIds = array_unique(array_filter(array_merge($participantIds, $phoneNumbers)));

        if (empty($searchIds)) {
            return;
        }

        // Query contacts table for names - try both exact matches and pattern matches
        // Only show contacts from sessions created by current user
        $contacts = Contact::where('waha_session_id', $this->group->waha_session_id)
            ->forUser(Auth::id())
            ->where(function ($query) use ($searchIds) {
                foreach ($searchIds as $id) {
                    // Try exact match first
                    $query->orWhere('wa_id', $id);

                    // Try to match phone number patterns (extract number from WhatsApp ID)
                    if (preg_match('/(\d+)@/', $id, $matches)) {
                        $phoneNumber = $matches[1];
                        $query->orWhere('wa_id', 'like', "%{$phoneNumber}%");
                    }
                }
            })
            ->get()
            ->keyBy('wa_id');

        // Map contact names back to participants
        foreach ($this->group->detail['participants'] as $participant) {
            $contactName = null;

            // Try multiple ways to find the contact
            $possibleIds = [];
            if (isset($participant['phoneNumber'])) {
                $possibleIds[] = $participant['phoneNumber'];
            }
            if (isset($participant['id'])) {
                $possibleIds[] = $participant['id'];
            }

            foreach ($possibleIds as $possibleId) {
                if (isset($contacts[$possibleId])) {
                    $contact = $contacts[$possibleId];
                    $contactName = $contact->verified_name ?: $contact->push_name ?: $contact->name;
                    break;
                }

                // Try to find by phone number pattern
                if (preg_match('/(\d+)@/', $possibleId, $matches)) {
                    $phoneNumber = $matches[1];
                    foreach ($contacts as $contact) {
                        if (str_contains($contact->wa_id, $phoneNumber)) {
                            $contactName = $contact->verified_name ?: $contact->push_name ?: $contact->name;
                            break 2;
                        }
                    }
                }
            }

            $this->participantNames[$participant['id']] = $contactName;
        }
    }

    public function previewImage($imageUrl, $participantId)
    {
        $this->previewImageUrl = $imageUrl;
        $this->previewParticipantId = $participantId;
        $this->modal('image-preview-modal')->show();
    }

    public function previewGroupImage()
    {
        if ($this->groupPictureUrl) {
            $this->previewImageUrl = $this->groupPictureUrl;
            $this->previewParticipantId = null; // Clear participant ID for group image
            $this->modal('image-preview-modal')->show();
        }
    }

    public function render()
    {
        return view('livewire.groups.groups-show');
    }
}
