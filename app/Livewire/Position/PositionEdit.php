<?php

namespace App\Livewire\Position;

use Livewire\Component;
use App\Models\Position;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Position')]
class PositionEdit extends Component
{
    public Position $position;

    public string $name;
    public string $description;

    public function mount(Position $position): void
    {
        $this->position = $position;

        $this->name = $position->name;
        $this->description = $position->description ?? '';
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $this->position->id,
            'description' => 'nullable|string',
        ]);

        // Store old values for logging
        $oldValues = [
            'name' => $this->position->name,
            'description' => $this->position->description,
        ];

        $this->position->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the update activity with detailed information
        activity()
            ->performedOn($this->position)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('updated position');

        session()->flash('success', 'Position updated.');

        return $this->redirect('/positions', true);
    }

    public function render()
    {
        return view('livewire.position.position-edit');
    }
}
