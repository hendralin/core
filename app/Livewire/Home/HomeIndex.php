<?php

namespace App\Livewire\Home;

use Livewire\Component;

class HomeIndex extends Component
{
    public function render()
    {
        return view('livewire.home.home-index')
            ->layout('layouts.landing', [
                'title' => config('app.name'),
            ]);
    }
}
