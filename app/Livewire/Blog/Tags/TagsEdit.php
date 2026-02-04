<?php

namespace App\Livewire\Blog\Tags;

use App\Models\Tag;
use Livewire\Component;
use App\Services\TagService;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Edit Blog Tag')]
class TagsEdit extends Component
{
    protected TagService $tagService;

    public Tag $tag;

    public $name;

    public function boot(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function mount(Tag $tag)
    {
        $this->tag = $tag;
        $this->name = $tag->name;
    }

    public function submit()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'unique:tags,name,' . $this->tag->id,
                'regex:/^[a-zA-Z0-9\s\-_]+$/'
            ],
        ], [
            'name.required' => 'Tag name is required.',
            'name.min' => 'Tag name must be at least 2 characters.',
            'name.max' => 'Tag name cannot exceed 50 characters.',
            'name.unique' => 'This tag name already exists.',
            'name.regex' => 'Tag name can only contain letters, numbers, spaces, hyphens, and underscores.',
        ]);

        try {
            $oldData = [
                'name' => $this->tag->name,
                'slug' => $this->tag->slug,
            ];

            $this->tag->update([
                'name' => trim($this->name),
            ]);

            // Log the update activity with detailed information
            activity()
                ->performedOn($this->tag)
                ->causedBy(Auth::user())
                ->withProperties([
                    'old' => $oldData,
                    'attributes' => [
                        'name' => trim($this->name),
                        'slug' => $this->tag->slug,
                    ]
                ])
                ->log('updated blog tag');

            session()->flash('success', 'Tag updated successfully.');

            return $this->redirect('/tags', true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update tag. Please try again.');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.blog.tags.tags-edit');
    }
}
