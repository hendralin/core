<?php

namespace App\Livewire\Blog\Tags;

use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\TagService;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Blog Tags')]
class TagsIndex extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected TagService $tagService;

    public $tagIdToDelete = null;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $sortableFields = ['name', 'posts_count', 'created_at', 'updated_at'];

    public function boot(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if (!in_array($field, $this->sortableFields)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function setTagToDelete($tagId)
    {
        $this->tagIdToDelete = $tagId;
    }

    public function delete()
    {
        try {
            if (!$this->tagIdToDelete) {
                session()->flash('error', 'No tag selected for deletion.');
                return;
            }

            $tag = Tag::findOrFail($this->tagIdToDelete);

            // Check if tag can be deleted using TagService
            $canDelete = $this->tagService->canDeleteTag($tag);

            if (!$canDelete['can_delete']) {
                session()->flash('error', implode(' ', $canDelete['errors']));
                return;
            }

            DB::transaction(function () use ($tag) {
                // Store tag data for logging before deletion
                $tagData = [
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];

                $tag->delete();

                // Log the deletion activity with detailed information
                activity()
                    ->performedOn($tag)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => $tagData
                    ])
                    ->log('deleted blog tag');
            });

            $this->reset(['tagIdToDelete']);

            session()->flash('success', 'Tag deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Tag not found.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $tags = $this->tagService->getEnhancedTagsForIndex(
            $this->search,
            $this->sortField,
            $this->sortDirection
        );

        return view('livewire.blog.tags.tags-index', compact('tags'));
    }

    public function getPerPageOptionsProperty()
    {
        return [5, 10, 25, 50];
    }
}
