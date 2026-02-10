<?php

namespace App\Livewire\Salary;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Salary;
use App\Models\Employee;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Audit Trail Penggajian')]
class SalaryAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedSalary = null;
    public $employees = [];

    public function mount()
    {
        $this->employees = Employee::orderBy('name')->get(['id', 'name']);
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedSalary'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedSalary']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Salary::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereRaw("EXISTS (SELECT 1 FROM salaries JOIN employees ON employees.id = salaries.employee_id WHERE salaries.id = activity_log.subject_id AND employees.name LIKE ?)", ['%' . $this->search . '%']);
                });
            })
            ->when($this->selectedSalary, function ($query) {
                $query->where('subject_id', $this->selectedSalary);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $stats = [
            'total_activities' => Activity::where('subject_type', Salary::class)->count(),
            'today_activities' => Activity::where('subject_type', Salary::class)->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Salary::class)->where('description', 'created salary')->count(),
            'updated_count' => Activity::where('subject_type', Salary::class)->where('description', 'updated salary')->count(),
            'deleted_count' => Activity::where('subject_type', Salary::class)->where('description', 'deleted salary')->count(),
        ];

        $salaries = Salary::with('employee')->orderBy('salary_date', 'desc')->limit(100)->get(['id', 'employee_id', 'salary_date']);
        $salaryOptions = $salaries->mapWithKeys(function ($s) {
            $label = ($s->employee?->name ?? 'ID#' . $s->id) . ' - ' . ($s->salary_date?->format('M Y') ?? '');
            return [$s->id => $label];
        })->toArray();

        return view('livewire.salary.salary-audit', compact('activities', 'stats', 'salaryOptions'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
