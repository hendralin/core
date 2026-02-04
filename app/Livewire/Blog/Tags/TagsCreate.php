<?php

namespace App\Livewire\Blog\Tags;

use App\Models\Tag;
use Livewire\Component;
use App\Services\TagService;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title('Create Blog Tag')]
class TagsCreate extends Component
{
    protected TagService $tagService;

    public $name;

    public function boot(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function submit()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'unique:tags,name',
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
            $tag = Tag::create([
                'name' => trim($this->name),
            ]);

            // Log the creation activity with detailed information
            activity()
                ->performedOn($tag)
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'name' => trim($this->name),
                        'slug' => $tag->slug,
                    ]
                ])
                ->log('created blog tag');

            session()->flash('success', 'Tag created successfully.');

            return $this->redirect('/tags', true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create tag. Please try again.');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.blog.tags.tags-create');
    }
}
