<?php

namespace App\Livewire\Employee;

use App\Models\Salary;
use Livewire\Component;
use App\Models\Employee;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Show Employee')]
class EmployeeShow extends Component
{
    use WithPagination;

    public Employee $employee;
    public $search = '';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function mount(Employee $employee): void
    {
        $this->employee = $employee;
    }

    public function render()
    {
        $totalEmployeesCount = Employee::count();

        // Always get the total count of items in this employee (through employee_salary_components and salaries)
        $employeeTotalSalaryComponents = $this->employee->employeeSalaryComponents()->count();
        $employeeTotalSalaries = $this->employee->salaries()->count();

        // Get employee salary components with salary component information
        $employeeSalaryComponentsQuery = $this->employee->employeeSalaryComponents()
            ->with(['salaryComponent'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('salaryComponent', function($salaryComponentQuery) {
                          $salaryComponentQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('updated_at', 'desc');

        $employeeSalaryComponents = $employeeSalaryComponentsQuery->paginate($this->perPage);

        // Get filtered count for display
        $filteredCount = $this->search ? $employeeSalaryComponentsQuery->count() : $employeeTotalSalaryComponents;

        // Calculate pagination info
        $startEmployeeSalaryComponent = ($employeeSalaryComponents->currentPage() - 1) * $employeeSalaryComponents->perPage() + 1;
        $endEmployeeSalaryComponent = min($startEmployeeSalaryComponent + $employeeSalaryComponents->perPage() - 1, $filteredCount);

        $paginationInfo = [
            'start' => $startEmployeeSalaryComponent,
            'end' => $endEmployeeSalaryComponent,
            'total' => $filteredCount,
            'is_filtered' => !empty($this->search)
        ];

        return view('livewire.employee.employee-show', compact(
            'totalEmployeesCount',
            'employeeSalaryComponents',
            'filteredCount',
            'paginationInfo',
            'employeeTotalSalaryComponents',
            'employeeTotalSalaries',
        ));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
