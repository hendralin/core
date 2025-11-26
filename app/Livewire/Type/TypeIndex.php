<?php

namespace App\Livewire\Type;

use Livewire\Component;
use App\Models\Type;
use App\Models\Brand;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\TypeExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Types')]
class TypeIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $typeIdToDelete = null;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $selectedBrand = null;
    public $brands = [];

    public function mount()
    {
        $this->brands = Brand::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage', 'selectedBrand'])) {
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

    public function clearFilters()
    {
        $this->reset(['search', 'selectedBrand']);
        $this->resetPage();
    }

    public function setTypeToDelete($typeId)
    {
        $this->typeIdToDelete = $typeId;
    }

    public function delete()
    {
        try {
            if (!$this->typeIdToDelete) {
                session()->flash('error', 'No type selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $type = Type::findOrFail($this->typeIdToDelete);

                // Store type data for logging before deletion
                $typeData = [
                    'name' => $type->name,
                    'description' => $type->description,
                ];

                $type->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($type)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $typeData
                    ])
                    ->log('deleted type');
            });

            $this->reset(['typeIdToDelete']);

            session()->flash('success', 'Type deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Type not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $types = Type::query()
            ->with(['brand', 'vehicles'])
            ->withCount('vehicles')
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('brand', function ($brandQuery) {
                            $brandQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                })
            )
            ->when($this->selectedBrand, function ($query) {
                $query->where('brand_id', $this->selectedBrand);
            })
            ->when($this->sortField === 'brand.name', function ($query) {
                $query->join('brands', 'types.brand_id', '=', 'brands.id')
                      ->orderBy('brands.name', $this->sortDirection)
                      ->select('types.*');
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate($this->perPage);

        return view('livewire.type.type-index', compact('types'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new TypeExport($this->search, $this->sortField, $this->sortDirection, $this->selectedBrand),
            'types_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $types = Type::query()
            ->with(['brand', 'vehicles'])
            ->withCount('vehicles')
            ->when(
                $this->search,
                fn($q) =>
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('brand', function ($brandQuery) {
                            $brandQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                })
            )
            ->when($this->selectedBrand, function ($query) {
                $query->where('brand_id', $this->selectedBrand);
            })
            ->when($this->sortField === 'brand.name', function ($query) {
                $query->join('brands', 'types.brand_id', '=', 'brands.id')
                      ->orderBy('brands.name', $this->sortDirection)
                      ->select('types.*');
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->get();

        $pdf = Pdf::loadView('exports.types-pdf', compact('types'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'types_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
