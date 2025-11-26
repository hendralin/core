<?php

namespace App\Livewire\Category;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use App\Exports\CategoryExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Categories')]
class CategoryIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $categoryIdToDelete = null;
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

    public function setCategoryToDelete($categoryId)
    {
        $this->categoryIdToDelete = $categoryId;
    }

    public function delete()
    {
        try {
            if (!$this->categoryIdToDelete) {
                session()->flash('error', 'No category selected for deletion.');
                return;
            }

            DB::transaction(function () {
                $category = Category::findOrFail($this->categoryIdToDelete);

                // Store category data for logging before deletion
                $categoryData = [
                    'name' => $category->name,
                    'description' => $category->description,
                ];

                $category->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($category)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $categoryData
                    ])
                    ->log('deleted category');
            });

            $this->reset(['categoryIdToDelete']);

            session()->flash('success', 'Category deleted.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Category not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $categories = Category::query()
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

        return view('livewire.category.category-index', compact('categories'));
    }

    public function exportExcel()
    {
        return Excel::download(
            new CategoryExport($this->search, $this->sortField, $this->sortDirection),
            'categories_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $categories = Category::query()
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

        $pdf = Pdf::loadView('exports.categories-pdf', compact('categories'));

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'categories_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
