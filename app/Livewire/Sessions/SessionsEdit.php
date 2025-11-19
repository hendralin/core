<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Edit Session')]
class SessionsEdit extends Component
{
    public Session $session;

    public function mount(Session $session)
    {
        $this->authorize('session.edit');

        $this->session = $session;
    }

    public function render()
    {
        return view('livewire.sessions.sessions-edit');
    }
}
