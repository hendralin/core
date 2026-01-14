<?php

namespace App\Livewire\Vehicle;

use Livewire\Component;
use App\Models\Vehicle;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\VehicleExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Vehicles')]
class VehicleIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $vehicleIdToDelete = null;
    public $search = '';
    public $statusFilter = '';
    public $sortField = 'police_number';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage', 'statusFilter'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function setVehicleToDelete($vehicleId)
    {
        $this->vehicleIdToDelete = $vehicleId;
    }


    public function delete()
    {
        try {
            if (!$this->vehicleIdToDelete) {
                session()->flash('error', 'No vehicle selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $vehicle = Vehicle::findOrFail($this->vehicleIdToDelete);

                // Store vehicle data for logging before deletion
                $vehicleData = [
                    'police_number' => $vehicle->police_number,
                    'brand_id' => $vehicle->brand_id,
                    'type_id' => $vehicle->type_id,
                    'category_id' => $vehicle->category_id,
                    'vehicle_model_id' => $vehicle->vehicle_model_id,
                    'year' => $vehicle->year,
                    'cylinder_capacity' => $vehicle->cylinder_capacity,
                    'chassis_number' => $vehicle->chassis_number,
                    'engine_number' => $vehicle->engine_number,
                    'color' => $vehicle->color,
                    'fuel_type' => $vehicle->fuel_type,
                    'kilometer' => $vehicle->kilometer,
                    'vehicle_registration_date' => $vehicle->vehicle_registration_date,
                    'vehicle_registration_expiry_date' => $vehicle->vehicle_registration_expiry_date,
                    'file_stnk' => $vehicle->file_stnk,
                    'warehouse_id' => $vehicle->warehouse_id,
                    'purchase_date' => $vehicle->purchase_date,
                    'purchase_price' => $vehicle->purchase_price,
                    'selling_date' => $vehicle->selling_date,
                    'selling_price' => $vehicle->selling_price,
                    'status' => $vehicle->status,
                    'description' => $vehicle->description,
                ];

                $vehicle->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($vehicle)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $vehicleData
                    ])
                    ->log('deleted vehicle');
            });

            $this->reset(['vehicleIdToDelete']);

            session()->flash('success', 'Vehicle deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Vehicle not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $vehicles = Vehicle::query()
            ->with(['brand', 'type', 'category', 'vehicle_model', 'warehouse', 'images'])
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
            ->paginate($this->perPage);

        return view('livewire.vehicle.vehicle-index', compact('vehicles'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new VehicleExport($this->search, $this->statusFilter, $this->sortField, $this->sortDirection),
            'vehicles_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $vehicles = Vehicle::query()
            ->with(['brand', 'type', 'category', 'vehicle_model', 'warehouse', 'images'])
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

        $pdf = Pdf::loadView('exports.vehicles-pdf', compact('vehicles'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'vehicles_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
