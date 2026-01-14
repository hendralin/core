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
        $vehicles = Vehicle::query()
            ->with(['brand', 'type', 'category', 'vehicle_model', 'warehouse'])
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
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        return view('exports.vehicles', [
            'vehicles' => $vehicles,
        ]);
    }
}

