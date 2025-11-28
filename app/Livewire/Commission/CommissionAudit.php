<?php

namespace App\Livewire\Commission;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Commission;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Commission Audit Trail')]
class CommissionAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedVehicle = null;
    public $commissionType = null;
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
        if (in_array($field, ['search', 'selectedVehicle', 'commissionType'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedVehicle()
    {
        $this->resetPage();
    }

    public function updatedCommissionType()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedVehicle', 'commissionType']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Commission::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Query the commissions table for description or amount
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM commissions WHERE commissions.id = activity_log.subject_id AND (commissions.description LIKE ? OR CAST(commissions.amount AS CHAR) LIKE ?))", ['%' . $this->search . '%', '%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVehicle, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM commissions WHERE commissions.id = activity_log.subject_id AND commissions.vehicle_id = ?)", [$this->selectedVehicle]);
            })
            ->when($this->commissionType, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM commissions WHERE commissions.id = activity_log.subject_id AND commissions.type = ?)", [$this->commissionType]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Commission::class)->count(),
            'today_activities' => Activity::where('subject_type', Commission::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Commission::class)
                ->where('description', 'created commission')->count(),
            'updated_count' => Activity::where('subject_type', Commission::class)
                ->where('description', 'updated commission')->count(),
            'deleted_count' => Activity::where('subject_type', Commission::class)
                ->where('description', 'deleted commission')->count(),
        ];

        return view('livewire.commission.commission-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }

    public function getCommissionTypeOptionsProperty()
    {
        return [
            1 => 'Komisi Penjualan',
            2 => 'Komisi Pembelian',
        ];
    }
}
