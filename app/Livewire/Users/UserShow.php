<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Show User')]
class UserShow extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user->load(['roles']); // Eager load relationships
    }

    public function render()
    {
        return view('livewire.users.user-show');
    }
}
