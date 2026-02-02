<?php

namespace App\Livewire\Position;

use Livewire\Component;
use App\Models\Position;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Position')]
class PositionCreate extends Component
{
    public $name, $description;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:positions,name',
            'description' => 'nullable|string',
        ]);

        $position = Position::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the creation activity with detailed information
        activity()
            ->performedOn($position)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('created position');

        session()->flash('success', 'Position created.');

        return $this->redirect('/positions', true);
    }

    public function render()
    {
        return view('livewire.position.position-create');
    }
}
