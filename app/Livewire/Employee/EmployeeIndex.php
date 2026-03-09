<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use App\Models\Employee;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\EmployeeExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Employees')]
class EmployeeIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $employeeIdToDelete = null;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    /** Filter: '' = semua, 'has_loan' = hanya yang punya sisa pinjaman > 0 */
    public $loanFilter = '';

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage', 'loanFilter'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function setEmployeeToDelete($employeeId)
    {
        $this->employeeIdToDelete = $employeeId;
    }

    public function delete()
    {
        try {
            if (!$this->employeeIdToDelete) {
                session()->flash('error', 'No employee selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $employee = Employee::findOrFail($this->employeeIdToDelete);

                // Store employee data for logging before deletion
                $employeeData = [
                    'user_id' => $employee->user_id,
                    'name' => $employee->name,
                    'join_date' => $employee->join_date,
                    'position_id' => $employee->position_id,
                    'status' => $employee->status,
                ];

                $employee->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($employee)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $employeeData
                    ])
                    ->log('deleted employee');
            });

            $this->reset(['employeeIdToDelete']);

            session()->flash('success', 'Employee deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Employee not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $employees = Employee::query()
            ->with(['position', 'user'])
            ->withCount(['employeeSalaryComponents', 'salaries'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('join_date', 'like', '%' . $this->search . '%')
                        ->orWhereHas('position', function($positionQuery) {
                            $positionQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('user', function($userQuery) {
                            $userQuery->where('email', 'like', '%' . $this->search . '%');
                        });
                })
            )
            ->when($this->loanFilter === 'has_loan', fn($q) => $q->where('remaining_loan', '>', 0))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.employee.employee-index', compact('employees'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new EmployeeExport($this->search, $this->sortField, $this->sortDirection),
            'employees_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $employees = Employee::query()
            ->with(['position', 'user'])
            ->withCount(['employeeSalaryComponents', 'salaries'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('join_date', 'like', '%' . $this->search . '%')
                        ->orWhereHas('position', function($positionQuery) {
                            $positionQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('user', function($userQuery) {
                            $userQuery->where('email', 'like', '%' . $this->search . '%');
                        });
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $pdf = Pdf::loadView('exports.employees-pdf', compact('employees'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'employees_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
