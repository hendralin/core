<?php

namespace App\Livewire\Brand;

use Livewire\Component;
use App\Models\Brand;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\BrandExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Brands')]
class BrandIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $brandIdToDelete = null;
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

    public function setBrandToDelete($brandId)
    {
        $this->brandIdToDelete = $brandId;
    }

    public function delete()
    {
        try {
            if (!$this->brandIdToDelete) {
                session()->flash('error', 'No brand selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $brand = Brand::findOrFail($this->brandIdToDelete);

                // Store brand data for logging before deletion
                $brandData = [
                    'name' => $brand->name,
                    'description' => $brand->description,
                ];

                $brand->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($brand)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $brandData
                    ])
                    ->log('deleted brand');
            });

            $this->reset(['brandIdToDelete']);

            session()->flash('success', 'Brand deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Brand not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $brands = Brand::query()
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

        return view('livewire.brand.brand-index', compact('brands'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new BrandExport($this->search, $this->sortField, $this->sortDirection),
            'brands_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $brands = Brand::query()
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

        $pdf = Pdf::loadView('exports.brands-pdf', compact('brands'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'brands_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
