<?php

namespace App\Livewire\Position;

use App\Models\Employee;
use Livewire\Component;
use App\Models\Position;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Show Position')]
class PositionShow extends Component
{
    use WithPagination;

    public Position $position;
    public $search = '';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function mount(Position $position): void
    {
        $this->position = $position;
    }

    public function render()
    {
        $totalEmployeesCount = Employee::count();

        // Always get the total count of items in this position (through employees)
        $positionTotalEmployees = $this->position->employees()->count();

        // Get employees with position information
        $employeesQuery = $this->position->employees()
            ->with(['position'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('updated_at', 'desc');

        $employees = $employeesQuery->paginate($this->perPage);

        // Get filtered count for display
        $filteredCount = $this->search ? $employeesQuery->count() : $positionTotalEmployees;

        // Calculate pagination info
        $startEmployee = ($employees->currentPage() - 1) * $employees->perPage() + 1;
        $endEmployee = min($startEmployee + $employees->perPage() - 1, $filteredCount);

        $paginationInfo = [
            'start' => $startEmployee,
            'end' => $endEmployee,
            'total' => $filteredCount,
            'is_filtered' => !empty($this->search)
        ];

        return view('livewire.position.position-show', compact(
            'totalEmployeesCount',
            'employees',
            'filteredCount',
            'paginationInfo',
            'positionTotalEmployees',
        ));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
