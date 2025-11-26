<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Warehouse;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Warehouse Audit Trail')]
class WarehouseAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedWarehouse = null;
    public $warehouses = [];

    public function mount()
    {
        $this->warehouses = Warehouse::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedWarehouse'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedWarehouse()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedWarehouse']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Warehouse::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Warehouse, we can directly query the warehouses table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM warehouses WHERE warehouses.id = activity_log.subject_id AND warehouses.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedWarehouse, function ($query) {
                $query->where('subject_id', $this->selectedWarehouse);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Warehouse::class)->count(),
            'today_activities' => Activity::where('subject_type', Warehouse::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Warehouse::class)
                ->where('description', 'created warehouse')->count(),
            'updated_count' => Activity::where('subject_type', Warehouse::class)
                ->where('description', 'updated warehouse')->count(),
            'deleted_count' => Activity::where('subject_type', Warehouse::class)
                ->where('description', 'deleted warehouse')->count(),
        ];

        return view('livewire.warehouse.warehouse-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
