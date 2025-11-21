<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Http;

#[Title('Show Contact')]
class ContactsShow extends Component
{
    public Contact $contact;
    public $profilePictureUrl;

    public function mount(Contact $contact): void
    {
        $this->contact = $contact->load(['wahaSession']); // Eager load relationships

        // Check if profile picture already exists in database, otherwise fetch from API
        $this->loadOrFetchProfilePicture();
    }

    private function loadOrFetchProfilePicture()
    {
        // First, check if we already have the profile picture URL in database
        if (!empty($this->contact->profile_picture_url)) {
            $this->profilePictureUrl = $this->contact->profile_picture_url;
            return;
        }

        // If not in database, fetch from WAHA API
        try {
            $response = Http::withHeaders([
                'accept' => '*/*',
                'X-Api-Key' => env('WAHA_API_KEY'),
            ])->get(env('WAHA_API_URL') . '/api/contacts/profile-picture', [
                'contactId' => $this->contact->wa_id,
                'refresh' => 'false',
                'session' => $this->contact->wahaSession->session_id
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $profilePictureUrl = $data['profilePictureURL'] ?? null;

                // Save to database for future use
                if ($profilePictureUrl) {
                    $this->contact->update(['profile_picture_url' => $profilePictureUrl]);
                }

                $this->profilePictureUrl = $profilePictureUrl;
            } else {
                // If API returns error, save null to avoid repeated calls
                $this->contact->update(['profile_picture_url' => null]);
                $this->profilePictureUrl = null;
            }
        } catch (\Exception $e) {
            // Silently fail if API call fails, save null to database to avoid repeated calls
            $this->contact->update(['profile_picture_url' => null]);
            $this->profilePictureUrl = null;
        }
    }

    public function render()
    {
        return view('livewire.contacts.contacts-show');
    }
}
