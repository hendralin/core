<?php

namespace App\Livewire\Vehicle;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Vehicle Audit Trail')]
class VehicleAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedVehicle = null;
    public $vehicles = [];

    public function mount()
    {
        $this->vehicles = Vehicle::select('id', 'police_number')->orderBy('police_number')->get();
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
            ->where('subject_type', Vehicle::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Query the vehicles table for police number
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM vehicles WHERE vehicles.id = activity_log.subject_id AND vehicles.police_number LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVehicle, function ($query) {
                $query->where('subject_id', $this->selectedVehicle);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Vehicle::class)->count(),
            'today_activities' => Activity::where('subject_type', Vehicle::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Vehicle::class)
                ->where('description', 'created vehicle')->count(),
            'updated_count' => Activity::where('subject_type', Vehicle::class)
                ->where('description', 'updated vehicle')->count(),
            'deleted_count' => Activity::where('subject_type', Vehicle::class)
                ->where('description', 'deleted vehicle')->count(),
        ];

        return view('livewire.vehicle.vehicle-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
