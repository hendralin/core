<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Session')]
class SessionsEdit extends Component
{
    public Session $session;

    public function mount(Session $session)
    {
        $this->authorize('session.edit');

        // Check if session belongs to current user
        if ($session->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to edit this session.');
        }

        $this->session = $session;
    }

    public function render()
    {
        return view('livewire.sessions.sessions-edit');
    }
}
