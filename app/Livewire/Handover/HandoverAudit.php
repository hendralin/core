<?php

namespace App\Livewire\Handover;

use Livewire\Component;
use App\Models\Activity;
use App\Models\VehicleHandover;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Handover Audit Trail')]
class HandoverAudit extends Component
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
            ->where('subject_type', VehicleHandover::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Query the vehicle_handovers table for handover_number, transferee, receiving_party or vehicle info
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM vehicle_handovers vh LEFT JOIN vehicles v ON vh.vehicle_id = v.id WHERE vh.id = activity_log.subject_id AND (vh.handover_number LIKE ? OR vh.transferee LIKE ? OR vh.receiving_party LIKE ? OR v.police_number LIKE ?))", ['%' . $this->search . '%', '%' . $this->search . '%', '%' . $this->search . '%', '%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVehicle, function ($query) {
                $query->whereRaw("EXISTS (SELECT 1 FROM vehicle_handovers WHERE vehicle_handovers.id = activity_log.subject_id AND vehicle_handovers.vehicle_id = ?)", [$this->selectedVehicle]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', VehicleHandover::class)->count(),
            'today_activities' => Activity::where('subject_type', VehicleHandover::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', VehicleHandover::class)
                ->where('description', 'created vehicle handover')->count(),
            'updated_count' => Activity::where('subject_type', VehicleHandover::class)
                ->where('description', 'updated vehicle handover')->count(),
            'deleted_count' => Activity::where('subject_type', VehicleHandover::class)
                ->where('description', 'deleted vehicle handover')->count(),
        ];

        return view('livewire.handover.handover-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
