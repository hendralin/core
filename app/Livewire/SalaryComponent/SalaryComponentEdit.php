<?php

namespace App\Livewire\SalaryComponent;

use Livewire\Component;
use App\Models\SalaryComponent;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Salary Component')]
class SalaryComponentEdit extends Component
{
    public SalaryComponent $salaryComponent;

    public string $name;
    public string $description;

    public function mount(SalaryComponent $salaryComponent): void
    {
        $this->salaryComponent = $salaryComponent;

        $this->name = $salaryComponent->name;
        $this->description = $salaryComponent->description ?? '';
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:salary_components,name,' . $this->salaryComponent->id,
            'description' => 'nullable|string',
        ]);

        // Store old values for logging
        $oldValues = [
            'name' => $this->salaryComponent->name,
            'description' => $this->salaryComponent->description,
        ];

        $this->salaryComponent->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the update activity with detailed information
        activity()
            ->performedOn($this->salaryComponent)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('updated salary component');

        session()->flash('success', 'Salary component updated.');

        return $this->redirect('/salary-components', true);
    }

    public function render()
    {
        return view('livewire.salary-component.salary-component-edit');
    }
}
