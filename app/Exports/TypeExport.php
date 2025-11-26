<?php

namespace App\Exports;

use App\Models\Type;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TypeExport implements FromView
{
    protected $search;
    protected $sortField;
    protected $sortDirection;
    protected $selectedBrand;

    public function __construct($search = '', $sortField = 'name', $sortDirection = 'asc', $selectedBrand = null)
    {
        $this->search = $search;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
        $this->selectedBrand = $selectedBrand;
    }

    public function view(): View
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

        return view('exports.types', [
            'types' => $types,
        ]);
    }
}
