<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Vendor;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\VendorExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Vendors')]
class VendorIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $vendorIdToDelete = null;
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

    public function setVendorToDelete($vendorId)
    {
        $this->vendorIdToDelete = $vendorId;
    }

    public function delete()
    {
        try {
            if (!$this->vendorIdToDelete) {
                session()->flash('error', 'No vendor selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $vendor = Vendor::findOrFail($this->vendorIdToDelete);

                // Store vendor data for logging before deletion
                $vendorData = [
                    'name' => $vendor->name,
                    'description' => $vendor->description,
                ];

                $vendor->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($vendor)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $vendorData
                    ])
                    ->log('deleted vendor');
            });

            $this->reset(['vendorIdToDelete']);

            session()->flash('success', 'Vendor deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Vendor not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $vendors = Vendor::query()
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('contact', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.vendor.vendor-index', compact('vendors'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new VendorExport($this->search, $this->sortField, $this->sortDirection),
            'vendors_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $vendors = Vendor::query()
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('contact', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $pdf = Pdf::loadView('exports.vendors-pdf', compact('vendors'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'vendors_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
