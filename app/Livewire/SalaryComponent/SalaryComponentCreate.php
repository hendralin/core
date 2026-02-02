<?php

namespace App\Livewire\SalaryComponent;

use Livewire\Component;
use App\Models\SalaryComponent;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Salary Component')]
class SalaryComponentCreate extends Component
{
    public $name, $description;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:salary_components,name',
            'description' => 'nullable|string',
        ]);

        $salaryComponent = SalaryComponent::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        // Log the creation activity with detailed information
        activity()
            ->performedOn($salaryComponent)
            ->causedBy(Auth::user())
            ->withProperties([
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            ])
            ->log('created salary component');

        session()->flash('success', 'Salary component created.');

        return $this->redirect('/salary-components', true);
    }

    public function render()
    {
        return view('livewire.salary-component.salary-component-create');
    }
}
