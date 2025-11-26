<?php

namespace App\Livewire\VehicleModel;

use App\Models\Vehicle;
use Livewire\Component;
use App\Models\VehicleModel;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Show Vehicle Model')]
class VehicleModelShow extends Component
{
    use WithPagination;

    public VehicleModel $vehicleModel;
    public $search = '';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function mount(VehicleModel $vehicleModel): void
    {
        $this->vehicleModel = $vehicleModel;
    }

    public function render()
    {
        $totalVehiclesCount = Vehicle::count();

        // Always get the total count of items in this vehicle model (through vehicles)
        $vehicleModelTotalVehicles = $this->vehicleModel->vehicles()->count();

        // Get vehicles with vehicle model information
        $vehiclesQuery = $this->vehicleModel->vehicles()
            ->with(['brand', 'type', 'category', 'warehouse'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('police_number', 'like', '%' . $this->search . '%')
                      ->orWhere('engine_number', 'like', '%' . $this->search . '%')
                      ->orWhere('chassis_number', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('updated_at', 'desc');

        $vehicles = $vehiclesQuery->paginate($this->perPage);

        // Get filtered count for display
        $filteredCount = $this->search ? $vehiclesQuery->count() : $vehicleModelTotalVehicles;

        // Calculate pagination info
        $startVehicle = ($vehicles->currentPage() - 1) * $vehicles->perPage() + 1;
        $endVehicle = min($startVehicle + $vehicles->perPage() - 1, $filteredCount);

        $paginationInfo = [
            'start' => $startVehicle,
            'end' => $endVehicle,
            'total' => $filteredCount,
            'is_filtered' => !empty($this->search)
        ];

        return view('livewire.vehicle-model.vehicle-model-show', compact(
            'totalVehiclesCount',
            'vehicles',
            'filteredCount',
            'paginationInfo',
            'vehicleModelTotalVehicles',
        ));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
