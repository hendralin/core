<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Employee;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Employee Audit Trail')]
class EmployeeAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedEmployee = null;
    public $employees = [];

    public function mount()
    {
        $this->employees = Employee::orderBy('name')->get();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedEmployee'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedEmployee()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedEmployee']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Employee::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Employee, we can directly query the employees table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM employees WHERE employees.id = activity_log.subject_id AND employees.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedEmployee, function ($query) {
                $query->where('subject_id', $this->selectedEmployee);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Employee::class)->count(),
            'today_activities' => Activity::where('subject_type', Employee::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Employee::class)
                ->where('description', 'created employee')->count(),
            'updated_count' => Activity::where('subject_type', Employee::class)
                ->where('description', 'updated employee')->count(),
            'deleted_count' => Activity::where('subject_type', Employee::class)
                ->where('description', 'deleted employee')->count(),
        ];

        return view('livewire.employee.employee-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
