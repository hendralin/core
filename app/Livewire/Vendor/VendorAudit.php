<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Vendor;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Vendor Audit Trail')]
class VendorAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedVendor = null;
    public $vendors = [];

    public function mount()
    {
        $this->vendors = Vendor::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedVendor'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedVendor()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedVendor']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Vendor::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Vendor, we can directly query the vendors table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM vendors WHERE vendors.id = activity_log.subject_id AND vendors.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedVendor, function ($query) {
                $query->where('subject_id', $this->selectedVendor);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Vendor::class)->count(),
            'today_activities' => Activity::where('subject_type', Vendor::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Vendor::class)
                ->where('description', 'created vendor')->count(),
            'updated_count' => Activity::where('subject_type', Vendor::class)
                ->where('description', 'updated vendor')->count(),
            'deleted_count' => Activity::where('subject_type', Vendor::class)
                ->where('description', 'deleted vendor')->count(),
        ];

        return view('livewire.vendor.vendor-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
