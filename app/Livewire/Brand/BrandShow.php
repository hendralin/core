<?php

namespace App\Livewire\Brand;

use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Brand;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Show Brand')]
class BrandShow extends Component
{
    use WithPagination;

    public Brand $brand;
    public $search = '';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function mount(Brand $brand): void
    {
        $this->brand = $brand;
    }

    public function render()
    {
        $totalVehiclesCount = Vehicle::count();

        // Always get the total count of items in this brand (through vehicles)
        $brandTotalVehicles = $this->brand->vehicles()->count();

        // Get vehicles with brand information
        $vehiclesQuery = $this->brand->vehicles()
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
        $filteredCount = $this->search ? $vehiclesQuery->count() : $brandTotalVehicles;

        // Calculate pagination info
        $startVehicle = ($vehicles->currentPage() - 1) * $vehicles->perPage() + 1;
        $endVehicle = min($startVehicle + $vehicles->perPage() - 1, $filteredCount);

        $paginationInfo = [
            'start' => $startVehicle,
            'end' => $endVehicle,
            'total' => $filteredCount,
            'is_filtered' => !empty($this->search)
        ];

        return view('livewire.brand.brand-show', compact(
            'totalVehiclesCount',
            'vehicles',
            'filteredCount',
            'paginationInfo',
            'brandTotalVehicles',
        ));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
