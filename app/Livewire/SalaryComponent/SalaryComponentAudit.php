<?php

namespace App\Livewire\SalaryComponent;

use Livewire\Component;
use App\Models\Activity;
use App\Models\SalaryComponent;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Salary Component Audit Trail')]
class SalaryComponentAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedSalaryComponent = null;
    public $salaryComponents = [];

    public function mount()
    {
        $this->salaryComponents = SalaryComponent::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedSalaryComponent'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedSalaryComponent()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedSalaryComponent']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', SalaryComponent::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always SalaryComponent, we can directly query the salary_components table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM salary_components WHERE salary_components.id = activity_log.subject_id AND salary_components.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedSalaryComponent, function ($query) {
                $query->where('subject_id', $this->selectedSalaryComponent);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', SalaryComponent::class)->count(),
            'today_activities' => Activity::where('subject_type', SalaryComponent::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', SalaryComponent::class)
                ->where('description', 'created salary component')->count(),
            'updated_count' => Activity::where('subject_type', SalaryComponent::class)
                ->where('description', 'updated salary component')->count(),
            'deleted_count' => Activity::where('subject_type', SalaryComponent::class)
                ->where('description', 'deleted salary component')->count(),
        ];

        return view('livewire.salary-component.salary-component-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
