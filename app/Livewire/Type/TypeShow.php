<?php

namespace App\Livewire\Type;

use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Type;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Show Type')]
class TypeShow extends Component
{
    use WithPagination;

    public Type $type;
    public $search = '';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function mount(Type $type): void
    {
        $this->type = $type;
    }

    public function render()
    {
        $totalVehiclesCount = Vehicle::count();

        // Always get the total count of items in this type
        $typeTotalVehicles = $this->type->vehicles()->count();

        // Load brand relationship
        $this->type->load('brand');

        // Get type vehicles with vehicle information
        $vehiclesQuery = $this->type->vehicles()
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
        $filteredCount = $this->search ? $vehiclesQuery->count() : $typeTotalVehicles;

        // Calculate pagination info
        $startVehicle = ($vehicles->currentPage() - 1) * $vehicles->perPage() + 1;
        $endVehicle = min($startVehicle + $vehicles->perPage() - 1, $filteredCount);

        $paginationInfo = [
            'start' => $startVehicle,
            'end' => $endVehicle,
            'total' => $filteredCount,
            'is_filtered' => !empty($this->search)
        ];

        return view('livewire.type.type-show', compact(
            'totalVehiclesCount',
            'vehicles',
            'filteredCount',
            'paginationInfo',
            'typeTotalVehicles',
        ));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
