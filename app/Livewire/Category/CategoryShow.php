<?php

namespace App\Livewire\Category;

use App\Models\Vehicle;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Show Category')]
class CategoryShow extends Component
{
    use WithPagination;

    public Category $category;
    public $search = '';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function mount(Category $category): void
    {
        $this->category = $category;
    }

    public function render()
    {
        $totalVehiclesCount = Vehicle::count();

        // Always get the total count of items in this category (through vehicles)
        $categoryTotalVehicles = $this->category->vehicles()->count();

        // Get vehicles with category information
        $vehiclesQuery = $this->category->vehicles()
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
        $filteredCount = $this->search ? $vehiclesQuery->count() : $categoryTotalVehicles;

        // Calculate pagination info
        $startVehicle = ($vehicles->currentPage() - 1) * $vehicles->perPage() + 1;
        $endVehicle = min($startVehicle + $vehicles->perPage() - 1, $filteredCount);

        $paginationInfo = [
            'start' => $startVehicle,
            'end' => $endVehicle,
            'total' => $filteredCount,
            'is_filtered' => !empty($this->search)
        ];

        return view('livewire.category.category-show', compact(
            'totalVehiclesCount',
            'vehicles',
            'filteredCount',
            'paginationInfo',
            'categoryTotalVehicles',
        ));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
