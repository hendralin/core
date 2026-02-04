@php
    $tagService = app(\App\Services\TagService::class);
@endphp

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Blog Tags') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all your blog tags') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <!-- Statistics Overview -->
    @php
        $totalTags = $tags->total();
        $activeTags = $tags->filter(fn($tag) => $tag->posts_count > 0)->count();
        $unusedTags = $totalTags - $activeTags;
        $popularTags = $tags->filter(fn($tag) => $tag->posts_count > 10)->count();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <flux:icon.tag class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $totalTags }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">Total Tags</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <flux:icon.document-text class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $activeTags }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">Active Tags</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <flux:icon.star class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $popularTags }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">Popular Tags</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 dark:bg-gray-900 rounded-lg">
                    <flux:icon.archive-box-x-mark class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $unusedTags }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">Unused Tags</div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4 mb-2">
        <!-- Actions Section -->
        <div class="flex flex-wrap gap-2">
            @can('blog.tag.create')
                <flux:button variant="primary" size="sm" href="{{ route('blog.tags.create') }}" wire:navigate icon="plus" tooltip="Create Tag">Create</flux:button>
            @endcan
            <div wire:loading>
                <flux:icon.loading class="text-red-600" />
            </div>
        </div>

        <!-- Filter Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 mb-3 mt-4">
            <div class="flex items-center gap-2 w-44 mb-2 md:mb-0">
                <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">Show:</label>
                <flux:select id="per-page" wire:model.live="perPage">
                    @foreach ($this->perPageOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                    @endforeach
                </flux:select>
                <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">entries</label>
            </div>
            <flux:spacer class="hidden md:inline" />
            <flux:spacer class="hidden md:inline" />
            <flux:input wire:model.live.debounce.500ms="search" icon="magnifying-glass" placeholder="Search tags" clearable />
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-200 text-sm text-left rtl:text-right text-gray-500 border dark:border-zinc-700 dark:text-zinc-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">No.</th>
                    <th scope="col" class="px-4 py-3 w-48">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'name') {{ $sortDirection }} @endif" wire:click="sortBy('name')">
                            Name
                            @if ($sortField == 'name' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'name' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-32">Slug</th>
                    <th scope="col" class="px-4 py-3 w-20">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'posts_count') {{ $sortDirection }} @endif" wire:click="sortBy('posts_count')">
                            Posts
                            @if ($sortField == 'posts_count' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'posts_count' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-28">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'created_at') {{ $sortDirection }} @endif" wire:click="sortBy('created_at')">
                            Created
                            @if ($sortField == 'created_at' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'created_at' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-28">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'updated_at') {{ $sortDirection }} @endif" wire:click="sortBy('updated_at')">
                            Updated
                            @if ($sortField == 'updated_at' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'updated_at' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-32 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tags) && $tags->count() > 0)
                    @foreach($tags as $index => $tag)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-white">{{ $tags->firstItem() + $index }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">
                                <div class="flex items-center gap-2">
                                    <flux:icon.tag class="w-4 h-4 text-gray-400" />
                                    {{ $tag->name }}
                                </div>
                            </td>
                            <td class="px-4 py-2 text-gray-600 dark:text-zinc-300">
                                <code class="text-xs bg-gray-100 dark:bg-zinc-700 px-2 py-1 rounded">{{ $tag->slug }}</code>
                            </td>
                            <td class="px-4 py-2 text-center text-gray-600 dark:text-zinc-300">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $tag->posts_count > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-zinc-700 dark:text-zinc-300' }}">
                                    {{ $tag->posts_count }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">
                                {{ $tag->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">
                                {{ $tag->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-center">
                                @can('blog.tag.view')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('blog.tags.show', $tag->id) }}" wire:navigate tooltip="Show">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                    </flux:button>
                                @endcan

                                @can('blog.tag.edit')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('blog.tags.edit', $tag->id) }}" wire:navigate tooltip="Edit">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>
                                @endcan

                                @can('blog.tag.delete')
                                    <flux:modal.trigger name="delete-tag">
                                        <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setTagToDelete({{ $tag->id }})" tooltip="Delete">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-gray-200">
                        <td class="px-4 py-2 text-gray-600 dark:text-zinc-300 text-center" colspan="7">
                            @if(isset($search) && !empty($search))
                                No results found for "{{ $search }}"
                            @else
                                No data available in table
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 mb-2">
        {{ $tags->links(data: ['scrollTo' => false]) }}
    </div>

    <flux:modal name="delete-tag" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete tag?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this tag.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">Delete Tag</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
