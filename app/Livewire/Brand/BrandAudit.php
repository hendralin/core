<?php

namespace App\Livewire\Brand;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Brand;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Brand Audit Trail')]
class BrandAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedBrand = null;
    public $brands = [];

    public function mount()
    {
        $this->brands = Brand::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedBrand'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedBrand()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedBrand']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Brand::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Brand, we can directly query the brands table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM brands WHERE brands.id = activity_log.subject_id AND brands.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedBrand, function ($query) {
                $query->where('subject_id', $this->selectedBrand);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Brand::class)->count(),
            'today_activities' => Activity::where('subject_type', Brand::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Brand::class)
                ->where('description', 'created brand')->count(),
            'updated_count' => Activity::where('subject_type', Brand::class)
                ->where('description', 'updated brand')->count(),
            'deleted_count' => Activity::where('subject_type', Brand::class)
                ->where('description', 'deleted brand')->count(),
        ];

        return view('livewire.brand.brand-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
