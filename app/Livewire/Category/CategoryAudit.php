<?php

namespace App\Livewire\Category;

use Livewire\Component;
use App\Models\Activity;
use App\Models\Category;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\WithoutUrlPagination;

#[Title('Category Audit Trail')]
class CategoryAudit extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedCategory = null;
    public $categories = [];

    public function mount()
    {
        $this->categories = Category::all();
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'selectedCategory'])) {
            $this->resetPage();
        }
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedCategory']);
        $this->resetPage();
    }

    public function render()
    {
        $activities = Activity::query()
            ->with(['causer', 'subject'])
            ->where('subject_type', Category::class)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('causer', function ($userQuery) {
                            $userQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhere(function ($subQuery) {
                            // Since we know subject_type is always Category, we can directly query the categories table
                            $subQuery->whereRaw("EXISTS (SELECT 1 FROM categories WHERE categories.id = activity_log.subject_id AND categories.name LIKE ?)", ['%' . $this->search . '%']);
                        });
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('subject_id', $this->selectedCategory);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get statistics
        $stats = [
            'total_activities' => Activity::where('subject_type', Category::class)->count(),
            'today_activities' => Activity::where('subject_type', Category::class)
                ->whereDate('created_at', today())->count(),
            'created_count' => Activity::where('subject_type', Category::class)
                ->where('description', 'created category')->count(),
            'updated_count' => Activity::where('subject_type', Category::class)
                ->where('description', 'updated category')->count(),
            'deleted_count' => Activity::where('subject_type', Category::class)
                ->where('description', 'deleted category')->count(),
        ];

        return view('livewire.category.category-audit', compact('activities', 'stats'));
    }

    public function getPerPageOptionsProperty()
    {
        return [10, 25, 50, 100];
    }
}
