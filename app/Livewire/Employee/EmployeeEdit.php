<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use App\Models\SalaryComponent;
use App\Models\EmployeeSalaryComponent;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title('Edit Employee')]
class EmployeeEdit extends Component
{
    public Employee $employee;

    public $user_id, $name, $join_date, $position_id, $status;
    public $selectedSalaryComponents = [];
    public $userSelected = false;

    public function mount(Employee $employee): void
    {
        $this->employee = $employee;

        $this->user_id = $employee->user_id;
        $this->name = $employee->name;
        $this->join_date = $employee->join_date->format('Y-m-d');
        $this->position_id = $employee->position_id;
        $this->status = $employee->status;

        // Load existing salary components
        $this->selectedSalaryComponents = $employee->employeeSalaryComponents->map(function($component) {
            return [
                'id' => $component->id,
                'salary_component_id' => $component->salary_component_id,
                'is_quantitative' => $component->is_quantitative,
                'amount' => number_format($component->amount, 0),
                'description' => $component->description,
            ];
        })->toArray();

        // Set userSelected flag if user is already linked
        $this->userSelected = $this->user_id ? true : false;
    }

    public function updatedUserId($value)
    {
        if ($value) {
            $user = User::find($value);
            if ($user) {
                $this->name = $user->name;
                $this->userSelected = true;
            }
        } else {
            // Jika user dihapus, reset flag agar user bisa edit manual name
            $this->userSelected = false;
        }
    }

    public function updatedName($value)
    {
        // Jika user mengubah name secara manual, set flag agar tidak di-override lagi
        if ($this->userSelected && $value !== User::find($this->user_id)?->name) {
            $this->userSelected = false;
        }
    }

    public function addSalaryComponent()
    {
        $this->selectedSalaryComponents[] = [
            'id' => null,
            'salary_component_id' => '',
            'is_quantitative' => false,
            'amount' => '',
            'description' => '',
        ];
    }

    public function removeSalaryComponent($index)
    {
        $component = $this->selectedSalaryComponents[$index];

        // If it has an ID, delete from database
        if (isset($component['id']) && $component['id']) {
            EmployeeSalaryComponent::find($component['id'])->delete();
        }

        unset($this->selectedSalaryComponents[$index]);
        $this->selectedSalaryComponents = array_values($this->selectedSalaryComponents);
    }

    public function submit()
    {
        $this->validate([
            'user_id' => 'nullable|exists:users,id|unique:employees,user_id,' . $this->employee->id,
            'name' => 'required|string|max:255',
            'join_date' => 'required|date',
            'position_id' => 'required|exists:positions,id',
            'status' => 'required|in:0,1,2',
            'selectedSalaryComponents' => 'array',
            'selectedSalaryComponents.*.salary_component_id' => 'required|exists:salary_components,id',
            'selectedSalaryComponents.*.is_quantitative' => 'boolean',
            'selectedSalaryComponents.*.amount' => 'required',
            'selectedSalaryComponents.*.description' => 'nullable|string|max:500',
        ]);

        // Validate unique salary components
        $salaryComponentIds = collect($this->selectedSalaryComponents)->pluck('salary_component_id');
        if ($salaryComponentIds->duplicates()->isNotEmpty()) {
            session()->flash('error', 'Duplicate salary components are not allowed.');
            return;
        }

        DB::transaction(function () {
            // Store old values for logging
            $oldValues = [
                'user_id' => $this->employee->user_id,
                'name' => $this->employee->name,
                'join_date' => $this->employee->join_date,
                'position_id' => $this->employee->position_id,
                'status' => $this->employee->status,
                'salary_components_count' => $this->employee->employeeSalaryComponents->count(),
            ];

            $this->employee->update([
                'user_id' => $this->user_id,
                'name' => $this->name,
                'join_date' => $this->join_date,
                'position_id' => $this->position_id,
                'status' => $this->status,
            ]);

            // Get existing component IDs
            $existingIds = collect($this->selectedSalaryComponents)
                ->whereNotNull('id')
                ->pluck('id')
                ->toArray();

            // Delete components that are no longer selected
            $this->employee->employeeSalaryComponents()
                ->whereNotIn('id', $existingIds)
                ->delete();

            // Update or create salary components
            foreach ($this->selectedSalaryComponents as $componentData) {
                EmployeeSalaryComponent::updateOrCreate(
                    ['id' => $componentData['id'] ?? null],
                    [
                        'employee_id' => $this->employee->id,
                        'salary_component_id' => $componentData['salary_component_id'],
                        'is_quantitative' => $componentData['is_quantitative'],
                        'amount' => str_replace(',', '', $componentData['amount']),
                        'description' => $componentData['description'] ?? null,
                    ]
                );
            }

            // Log the update activity with detailed information
            activity()
                ->performedOn($this->employee)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $oldValues,
                    'attributes' => [
                        'user_id' => $this->user_id,
                        'name' => $this->name,
                        'join_date' => $this->join_date,
                        'position_id' => $this->position_id,
                        'status' => $this->status,
                        'salary_components_count' => count($this->selectedSalaryComponents),
                    ]
                ])
                ->log('updated employee');
        });

        session()->flash('success', 'Employee updated with salary components.');

        return $this->redirect('/employees', true);
    }

    public function render()
    {
        $positions = Position::orderBy('name')->get();
        $availableUsers = User::where(function($query) {
            $query->whereDoesntHave('employee')
                  ->orWhere('id', $this->employee->user_id);
        })->orderBy('name')->get();
        $salaryComponents = SalaryComponent::orderBy('name')->get();

        return view('livewire.employee.employee-edit', compact('positions', 'availableUsers', 'salaryComponents'));
    }
}
