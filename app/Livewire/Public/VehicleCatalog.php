<?php

namespace App\Livewire\Public;

use App\Models\Vehicle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

#[Title('Katalog Kendaraan')]
#[Layout('layouts.public')]
class VehicleCatalog extends Component
{
    use WithPagination, WithoutUrlPagination;

    public string $search = '';
    public ?int $brandId = null;
    public ?int $typeId = null;
    public ?int $minYear = null;
    public ?int $maxYear = null;
    public ?int $minPrice = null;
    public ?int $maxPrice = null;
    public int $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'brandId' => ['except' => null],
        'typeId' => ['except' => null],
        'minYear' => ['except' => null],
        'maxYear' => ['except' => null],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'page' => ['except' => 1],
    ];

    public function updating($field): void
    {
        if (in_array($field, ['search', 'brandId', 'typeId', 'minYear', 'maxYear', 'minPrice', 'maxPrice', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'brandId',
            'typeId',
            'minYear',
            'maxYear',
            'minPrice',
            'maxPrice',
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $query = Vehicle::query()
            ->with(['brand', 'type', 'vehicle_model', 'images'])
            ->where('display_price', '>', 0)
            ->where('status', '1'); // hanya kendaraan Available

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('police_number', 'like', '%' . $search . '%')
                    ->orWhereHas('brand', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('type', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('vehicle_model', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($this->brandId) {
            $query->where('brand_id', $this->brandId);
        }

        if ($this->typeId) {
            $query->where('type_id', $this->typeId);
        }

        if ($this->minYear) {
            $query->where('year', '>=', $this->minYear);
        }

        if ($this->maxYear) {
            $query->where('year', '<=', $this->maxYear);
        }

        if ($this->minPrice) {
            $query->where('display_price', '>=', $this->minPrice);
        }

        if ($this->maxPrice) {
            $query->where('display_price', '<=', $this->maxPrice);
        }

        $vehicles = $query
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        // Ambil hanya brand dan type yang memang dipakai oleh vehicle (status Available)
        $brandIds = Vehicle::where('status', '1')
            ->whereNotNull('brand_id')
            ->distinct()
            ->pluck('brand_id');

        $typeIds = Vehicle::where('status', '1')
            ->whereNotNull('type_id')
            ->distinct()
            ->pluck('type_id');

        $brands = \App\Models\Brand::whereIn('id', $brandIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $types = \App\Models\Type::whereIn('id', $typeIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.public.vehicle-catalog', [
            'vehicles' => $vehicles,
            'brands' => $brands,
            'types' => $types,
        ]);
    }
}

