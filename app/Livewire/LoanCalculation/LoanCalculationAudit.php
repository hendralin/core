<?php

namespace App\Livewire\LoanCalculation;

use Livewire\Component;
use App\Models\Activity;
use App\Models\LoanCalculation;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Loan Calculation Audit Trail')]
class LoanCalculationAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedVehicle = null;
    public $vehicles = [];

    public function mount()
    {
        $this->vehicles = Vehicle::select('id', 'police_number')->orderBy('police_number')->get();

        // Check for selectedVehicle from URL parameters
        if (request()->has('selectedVehicle')) {
            $this->selectedVehicle = request()->get('selectedVehicle');
        }
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedVehicle'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedVehicle()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedVehicle']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', LoanCalculation::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Query the loan_calculations table for description or leasing name
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM loan_calculations lc LEFT JOIN leasings l ON lc.leasing_id = l.id WHERE lc.id = activity_log.subject_id AND (lc.description LIKE ? OR l.name LIKE ?))", ['%' . $this->search . '%', '%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVehicle, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM loan_calculations WHERE loan_calculations.id = activity_log.subject_id AND loan_calculations.vehicle_id = ?)", [$this->selectedVehicle]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', LoanCalculation::class)->count(),
            'today_activities' => Activity::where('subject_type', LoanCalculation::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', LoanCalculation::class)
                ->where('description', 'created loan calculation')->count(),
            'updated_count' => Activity::where('subject_type', LoanCalculation::class)
                ->where('description', 'updated loan calculation')->count(),
            'deleted_count' => Activity::where('subject_type', LoanCalculation::class)
                ->where('description', 'deleted loan calculation')->count(),
        ];

        return view('livewire.loan-calculation.loan-calculation-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
