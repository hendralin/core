<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Warehouse;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\WarehouseExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Warehouses')]
class WarehouseIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $warehouseIdToDelete = null;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 5;

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

    public function setWarehouseToDelete($warehouseId)
    {
        $this->warehouseIdToDelete = $warehouseId;
    }

    public function delete()
    {
        try {
            if (!$this->warehouseIdToDelete) {
                session()->flash('error', 'No warehouse selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $warehouse = Warehouse::findOrFail($this->warehouseIdToDelete);

                // Store warehouse data for logging before deletion
                $warehouseData = [
                    'name' => $warehouse->name,
                    'address' => $warehouse->address,
                ];

                $warehouse->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($warehouse)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $warehouseData
                    ])
                    ->log('deleted warehouse');
            });

            $this->reset(['warehouseIdToDelete']);

            session()->flash('success', 'Warehouse deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Warehouse not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $warehouses = Warehouse::query()
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.warehouse.warehouse-index', compact('warehouses'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new WarehouseExport($this->search, $this->sortField, $this->sortDirection),
            'warehouses_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $warehouses = Warehouse::query()
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $pdf = Pdf::loadView('exports.warehouses-pdf', compact('warehouses'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'warehouses_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
