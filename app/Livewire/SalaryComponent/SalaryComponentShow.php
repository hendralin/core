<?php

namespace App\Livewire\SalaryComponent;

use App\Models\Employee;
use Livewire\Component;
use App\Models\SalaryComponent;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Show Salary Component')]
class SalaryComponentShow extends Component
{
    use WithPagination;

    public SalaryComponent $salaryComponent;
    public $search = '';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function mount(SalaryComponent $salaryComponent): void
    {
        $this->salaryComponent = $salaryComponent;
    }

    public function render()
    {
        $totalEmployeeSalaryComponentsCount = Employee::count();

        // Always get the total count of items in this salary component (through employee_salary_components)
        $salaryComponentTotalEmployees = $this->salaryComponent->employeeSalaryComponents()->count();

        // Get employee salary components with employee information
        $employeeSalaryComponentsQuery = $this->salaryComponent->employeeSalaryComponents()
            ->with(['employee'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->whereHas('employee', function($employeeQuery) {
                        $employeeQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('updated_at', 'desc');

        $employeeSalaryComponents = $employeeSalaryComponentsQuery->paginate($this->perPage);

        // Get filtered count for display
        $filteredCount = $this->search ? $employeeSalaryComponentsQuery->count() : $salaryComponentTotalEmployees;

        // Calculate pagination info
        $startEmployeeSalaryComponent = ($employeeSalaryComponents->currentPage() - 1) * $employeeSalaryComponents->perPage() + 1;
        $endEmployeeSalaryComponent = min($startEmployeeSalaryComponent + $employeeSalaryComponents->perPage() - 1, $filteredCount);

        $paginationInfo = [
            'start' => $startEmployeeSalaryComponent,
            'end' => $endEmployeeSalaryComponent,
            'total' => $filteredCount,
            'is_filtered' => !empty($this->search)
        ];

        return view('livewire.salary-component.salary-component-show', compact(
            'totalEmployeeSalaryComponentsCount',
            'employeeSalaryComponents',
            'filteredCount',
            'paginationInfo',
            'salaryComponentTotalEmployees',
        ));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
