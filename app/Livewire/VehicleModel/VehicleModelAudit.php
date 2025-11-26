<?php

namespace App\Livewire\VehicleModel;

use Livewire\Component;
use App\Models\Activity;
use App\Models\VehicleModel;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Vehicle Model Audit Trail')]
class VehicleModelAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedVehicleModel = null;
    public $vehicleModels = [];

    public function mount()
    {
        $this->vehicleModels = VehicleModel::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedVehicleModel'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedVehicleModel()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedVehicleModel']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', VehicleModel::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always VehicleModel, we can directly query the vehicle_models table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM vehicle_models WHERE vehicle_models.id = activity_log.subject_id AND vehicle_models.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVehicleModel, function ($query) {
                $query->where('subject_id', $this->selectedVehicleModel);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', VehicleModel::class)->count(),
            'today_activities' => Activity::where('subject_type', VehicleModel::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', VehicleModel::class)
                ->where('description', 'created vehicle model')->count(),
            'updated_count' => Activity::where('subject_type', VehicleModel::class)
                ->where('description', 'updated vehicle model')->count(),
            'deleted_count' => Activity::where('subject_type', VehicleModel::class)
                ->where('description', 'deleted vehicle model')->count(),
        ];

        return view('livewire.vehicle-model.vehicle-model-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
