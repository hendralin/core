<?php

namespace App\Exports;

use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VehicleExport implements FromView
{
    protected $search;
    protected $statusFilter;
    protected $sortField;
    protected $sortDirection;

    public function __construct($search = '', $statusFilter = '', $sortField = 'police_number', $sortDirection = 'asc')
    {
        $this->search = $search;
        $this->statusFilter = $statusFilter;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
    }

    public function view(): View
    {
        $query = Vehicle::query()
            ->with(['brand', 'type', 'category', 'vehicle_model', 'warehouse', 'costs', 'commissions'])
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('police_number', 'like', '%' . $this->search . '%')
                        ->orWhere('chassis_number', 'like', '%' . $this->search . '%')
                        ->orWhere('engine_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('brand', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('type', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('category', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('vehicle_model', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('warehouse', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                })
            )
            ->when(
                $this->statusFilter !== '',
                fn($q) => $q->where('status', $this->statusFilter)
            );

        // Apply sorting including related name fields (brand/type/model)
        $query = $this->applySorting($query);

        $vehicles = $query->get();

        return view('exports.vehicles', [
            'vehicles' => $vehicles,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    /**
     * Apply sorting to the query, including related name fields.
     */
    private function applySorting($query)
    {
        return match ($this->sortField) {
            'brand_name' => $query
                ->join('brands', 'vehicles.brand_id', '=', 'brands.id')
                ->orderBy('brands.name', $this->sortDirection)
                ->select('vehicles.*'),
            'type_name' => $query
                ->join('types', 'vehicles.type_id', '=', 'types.id')
                ->orderBy('types.name', $this->sortDirection)
                ->select('vehicles.*'),
            'vehicle_model_name' => $query
                ->join('vehicle_models', 'vehicles.vehicle_model_id', '=', 'vehicle_models.id')
                ->orderBy('vehicle_models.name', $this->sortDirection)
                ->select('vehicles.*'),
            default => $query->orderBy($this->sortField, $this->sortDirection),
        };
    }
}

