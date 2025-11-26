<?php

namespace App\Livewire\VehicleModel;

use Livewire\Component;
use App\Models\VehicleModel;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\VehicleModelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Vehicle Models')]
class VehicleModelIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $vehicleModelIdToDelete = null;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
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

    public function setVehicleModelToDelete($vehicleModelId)
    {
        $this->vehicleModelIdToDelete = $vehicleModelId;
    }

    public function delete()
    {
        try {
            if (!$this->vehicleModelIdToDelete) {
                session()->flash('error', 'No vehicle model selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $vehicleModel = VehicleModel::findOrFail($this->vehicleModelIdToDelete);

                // Store vehicle model data for logging before deletion
                $vehicleModelData = [
                    'name' => $vehicleModel->name,
                    'description' => $vehicleModel->description,
                ];

                $vehicleModel->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($vehicleModel)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $vehicleModelData
                    ])
                    ->log('deleted vehicle model');
            });

            $this->reset(['vehicleModelIdToDelete']);

            session()->flash('success', 'Vehicle model deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Vehicle model not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $vehicleModels = VehicleModel::query()
            ->withCount('vehicles')
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.vehicle-model.vehicle-model-index', compact('vehicleModels'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new VehicleModelExport($this->search, $this->sortField, $this->sortDirection),
            'vehicle_models_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $vehicleModels = VehicleModel::query()
            ->withCount('vehicles')
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $pdf = Pdf::loadView('exports.vehicle-models-pdf', compact('vehicleModels'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'vehicle_models_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
