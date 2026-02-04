<?php

namespace App\Livewire\Blog\Tags;

use App\Models\Tag;
use Livewire\Component;
use App\Services\TagService;
use Livewire\Attributes\Title;

#[Title('View Blog Tag')]
class TagsShow extends Component
{
    protected TagService $tagService;

    public Tag $tag;

    public function boot(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function mount(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function render()
    {
        $statistics = $this->tagService->getTagStatistics($this->tag);
        $usageStatus = $this->tagService->getTagUsageStatus($this->tag);
        $recentPosts = $this->tagService->formatPostsForDisplay($this->tag, 5);

        return view('livewire.blog.tags.tags-show', compact(
            'statistics',
            'usageStatus',
            'recentPosts'
        ));
    }
}
