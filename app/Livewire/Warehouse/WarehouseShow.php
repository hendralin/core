<?php

namespace App\Livewire\Warehouse;

use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Warehouse;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Show Warehouse')]
class WarehouseShow extends Component
{
    use WithPagination;

    public Warehouse $warehouse;
    public $search = '';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function mount(Warehouse $warehouse): void
    {
        $this->warehouse = $warehouse;
    }

    public function render()
    {
        $totalVehiclesCount = Vehicle::count();

        // Always get the total count of vehicles in this warehouse
        $warehouseTotalVehicles = $this->warehouse->vehicles()->count();

        // Get warehouse vehicles with vehicle information
        $vehiclesQuery = $this->warehouse->vehicles()
            ->with(['brand', 'type', 'category', 'warehouse', 'vehicle_model'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('police_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('vehicle_model', function($qm) {
                          $qm->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('brand', function($qb) {
                          $qb->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('updated_at', 'desc');

        $vehicles = $vehiclesQuery->paginate($this->perPage);

        // Get filtered count for display
        $filteredCount = $this->search ? $vehiclesQuery->count() : $warehouseTotalVehicles;

        // Calculate pagination info
        $startVehicle = ($vehicles->currentPage() - 1) * $vehicles->perPage() + 1;
        $endVehicle = min($startVehicle + $vehicles->perPage() - 1, $filteredCount);

        $paginationInfo = [
            'start' => $startVehicle,
            'end' => $endVehicle,
            'total' => $filteredCount,
            'is_filtered' => !empty($this->search)
        ];

        return view('livewire.warehouse.warehouse-show', compact(
            'totalVehiclesCount',
            'vehicles',
            'filteredCount',
            'paginationInfo',
            'warehouseTotalVehicles',
        ));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
