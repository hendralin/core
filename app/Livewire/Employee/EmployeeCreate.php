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
use Illuminate\Validation\Rule;

#[Title('Create Employee')]
class EmployeeCreate extends Component
{
    public $user_id, $name, $join_date, $position_id, $status = 1;
    public $selectedSalaryComponents = [];
    public $userSelected = false;

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
            'salary_component_id' => '',
            'is_quantitative' => false,
            'amount' => '',
            'description' => '',
        ];
    }

    public function removeSalaryComponent($index)
    {
        unset($this->selectedSalaryComponents[$index]);
        $this->selectedSalaryComponents = array_values($this->selectedSalaryComponents);
    }

    public function submit()
    {
        $this->validate([
            'user_id' => 'nullable|exists:users,id|unique:employees,user_id',
            'name' => 'required|string|max:255',
            'join_date' => 'required|date',
            'position_id' => 'required|exists:positions,id',
            'status' => 'required|in:0,1,2',
            'selectedSalaryComponents' => 'array',
            'selectedSalaryComponents.*.salary_component_id' => 'required|exists:salary_components,id',
            'selectedSalaryComponents.*.is_quantitative' => 'boolean',
            'selectedSalaryComponents.*.amount' => 'required',
            'selectedSalaryComponents.*.description' => 'nullable|string|max:500',
        ], [
            'user_id.unique' => 'User account already linked to another employee.',
            'name.required' => 'Employee name is required.',
            'name.string' => 'Employee name must be a string.',
            'name.max' => 'Employee name must be less than 255 characters.',
            'join_date.required' => 'Join date is required.',
            'join_date.date' => 'Join date must be a date.',
            'position_id.required' => 'Position is required.',
            'position_id.exists' => 'Position is invalid.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status is invalid.',
            'selectedSalaryComponents.array' => 'Selected salary components must be an array.',
            'selectedSalaryComponents.*.salary_component_id.required' => 'Salary component is required.',
            'selectedSalaryComponents.*.salary_component_id.exists' => 'Salary component is invalid.',
            'selectedSalaryComponents.*.is_quantitative.boolean' => 'Is quantitative must be a boolean.',
            'selectedSalaryComponents.*.amount.required' => 'Amount is required.',
            'selectedSalaryComponents.*.description.string' => 'Description must be a string.',
            'selectedSalaryComponents.*.description.max' => 'Description must be less than 500 characters.',
        ]);

        // Validate unique salary components
        $salaryComponentIds = collect($this->selectedSalaryComponents)->pluck('salary_component_id');
        if ($salaryComponentIds->duplicates()->isNotEmpty()) {
            session()->flash('error', 'Duplicate salary components are not allowed.');
            return;
        }

        DB::transaction(function () {
            $employee = Employee::create([
                'user_id' => $this->user_id,
                'name' => $this->name,
                'join_date' => $this->join_date,
                'position_id' => $this->position_id,
                'status' => $this->status,
            ]);

            // Create employee salary components
            foreach ($this->selectedSalaryComponents as $salaryComponentData) {
                EmployeeSalaryComponent::create([
                    'employee_id' => $employee->id,
                    'salary_component_id' => $salaryComponentData['salary_component_id'],
                    'is_quantitative' => $salaryComponentData['is_quantitative'],
                    'amount' => str_replace(',', '', $salaryComponentData['amount']),
                    'description' => $salaryComponentData['description'] ?? null,
                ]);
            }

            // Log the creation activity with detailed information
            activity()
                ->performedOn($employee)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'user_id' => $this->user_id,
                        'name' => $this->name,
                        'join_date' => $this->join_date,
                        'position_id' => $this->position_id,
                        'status' => $this->status,
                        'salary_components_count' => count($this->selectedSalaryComponents),
                    ]
                ])
                ->log('created employee');
        });

        session()->flash('success', 'Employee created with salary components.');

        return $this->redirect('/employees', true);
    }

    public function render()
    {
        $positions = Position::orderBy('name')->get();
        $availableUsers = User::whereDoesntHave('employee')->orderBy('name')->get();
        $salaryComponents = SalaryComponent::orderBy('name')->get();

        return view('livewire.employee.employee-create', compact('positions', 'availableUsers', 'salaryComponents'));
    }
}
