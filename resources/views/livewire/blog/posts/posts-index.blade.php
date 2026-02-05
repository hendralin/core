<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Posts') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your blog posts') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    {{-- Statistics Overview --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <flux:icon.document-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total'] }}</div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Total Posts') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['published'] }}</div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Published') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-amber-100 dark:bg-amber-900 rounded-lg">
                    <flux:icon.pencil-square class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['draft'] }}</div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Draft') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                    <flux:icon.archive-box class="w-6 h-6 text-zinc-600 dark:text-zinc-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['archived'] }}</div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Archived') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Create --}}
    <div class="space-y-4 mb-2">
        <div class="flex flex-wrap gap-2 items-center">
            @can('blog.post.create')
                <flux:button variant="primary" size="sm" href="{{ route('blog.posts.create') }}" wire:navigate icon="plus" tooltip="{{ __('New Post') }}">
                    {{ __('New Post') }}
                </flux:button>
            @endcan
            <div wire:loading>
                <flux:icon.loading class="text-red-600" />
            </div>
        </div>

        {{-- Status & Author filters --}}
        <div class="flex flex-wrap gap-3 items-center mt-4">
            <div class="flex items-center gap-2">
                <label for="filter-status" class="text-sm font-medium text-zinc-700 dark:text-zinc-300 whitespace-nowrap">{{ __('Status') }}</label>
                <flux:select id="filter-status" wire:model.live="status" class="w-44">
                    <flux:select.option value="all">{{ __('All Posts') }}</flux:select.option>
                    <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
                    <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
                    <flux:select.option value="archived">{{ __('Archived') }}</flux:select.option>
                </flux:select>
            </div>
            @if (!$authors->isEmpty())
                <div class="flex items-center gap-2">
                    <label for="filter-author" class="text-sm font-medium text-zinc-700 dark:text-zinc-300 whitespace-nowrap">{{ __('Author') }}</label>
                    <flux:select id="filter-author" wire:model.live="author" class="w-48">
                        <flux:select.option value="all">{{ __('All Authors') }}</flux:select.option>
                        @foreach ($authors as $authorUser)
                            <flux:select.option value="{{ $authorUser->id }}">{{ $authorUser->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            @endif
            @if ($this->hasActiveFilters)
                <flux:button variant="ghost" wire:click="resetFilters" icon="x-mark">
                    {{ __('Reset filters') }}
                </flux:button>
            @endif
        </div>

        {{-- Filter Section: per-page & search --}}
        <div class="grid grid-cols-1 md:grid-cols-4 mb-3 mt-4">
            <div class="flex items-center gap-2 w-44 mb-2 md:mb-0">
                <label for="per-page-posts" class="text-sm text-zinc-700 dark:text-zinc-300">{{ __('Show:') }}</label>
                <flux:select id="per-page-posts" wire:model.live="perPage">
                    @foreach ($this->perPageOptions as $option)
                    <flux:select.option value="{{ $option }}">{{ is_string($option) ? __('All') : $option }}</flux:select.option>
                    @endforeach
                </flux:select>
                <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ __('entries') }}</span>
            </div>
            <flux:spacer class="hidden md:inline" />
            <flux:spacer class="hidden md:inline" />
            <div class="flex flex-wrap gap-2 items-center flex-1 min-w-0">
                <label for="filter-search" class="sr-only">{{ __('Search') }}</label>
                <flux:input
                    id="filter-search"
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    placeholder="{{ __('Search posts...') }}"
                    clearable
                    class="min-w-48 max-w-xs"
                />
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <table class="w-full min-w-200 text-sm text-left rtl:text-right text-zinc-500 dark:text-zinc-400">
            <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">{{ __('No.') }}</th>
                    <th scope="col" class="px-4 py-3">{{ __('Title') }}</th>
                    <th scope="col" class="px-4 py-3 w-40">{{ __('Categories') }}</th>
                    <th scope="col" class="px-4 py-3 w-32">{{ __('Author') }}</th>
                    <th scope="col" class="px-4 py-3 w-28">{{ __('Status') }}</th>
                    <th scope="col" class="px-4 py-3 w-28">{{ __('Created') }}</th>
                    <th scope="col" class="px-4 py-3 w-32 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr
                        wire:key="post-{{ $post->id }}"
                        class="odd:bg-white odd:dark:bg-zinc-900 even:bg-zinc-50 even:dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800/50"
                        wire:loading.class="opacity-50"
                    >
                        <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-start gap-3">
                                @if ($post->featured_image_url)
                                    <img
                                        src="{{ $post->featured_image_url }}"
                                        alt="{{ $post->title }}"
                                        class="w-14 h-14 rounded object-cover border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 shrink-0"
                                    >
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $post->title }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ Str::limit($post->excerpt, 100) }}</div>
                                    @if ($post->comments_count > 0)
                                        <div class="mt-1">
                                            <flux:badge size="sm" color="blue">
                                                {{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}
                                            </flux:badge>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($post->categories as $category)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                        style="background-color: {{ $category->color }}20; color: {{ $category->color }};"
                                    >
                                        {{ $category->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('No category') }}</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-zinc-600 dark:text-zinc-300">
                            {{ $post->user->name }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if ($post->status === 'published')
                                <flux:badge size="sm" color="green">{{ ucfirst($post->status) }}</flux:badge>
                            @elseif ($post->status === 'draft')
                                <flux:badge size="sm" color="amber">{{ ucfirst($post->status) }}</flux:badge>
                            @else
                                <flux:badge size="sm" color="zinc">{{ ucfirst($post->status) }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $post->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <div class="flex justify-end gap-1">
                                @can('blog.post.view')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('blog.posts.show', $post) }}" wire:navigate tooltip="{{ __('Show') }}">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-400" />
                                    </flux:button>
                                @endcan

                                @if (auth()->user()->can('blog.post.edit.all') || (auth()->user()->can('blog.post.edit.own') && $post->user_id === auth()->id()))
                                    <flux:button variant="ghost" size="xs" square href="{{ route('blog.posts.edit', $post) }}" wire:navigate tooltip="{{ __('Edit') }}">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-400" />
                                    </flux:button>
                                @endif

                                @if (auth()->user()->can('blog.post.delete.all') || (auth()->user()->can('blog.post.delete.own') && $post->user_id === auth()->id()))
                                    <flux:modal.trigger name="delete-post">
                                        <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setPostToDelete({{ $post->id }})" tooltip="{{ __('Delete') }}">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-400" />
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endif

                                @can('blog.post.audit')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('blog.posts.audit', $post) }}" wire:navigate tooltip="{{ __('Audit Trail') }}">
                                        <flux:icon.document-text variant="mini" class="text-zinc-500 dark:text-zinc-300" />
                                    </flux:button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12">
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <svg class="mx-auto h-16 w-16 text-zinc-300 dark:text-zinc-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                                    @if ($search !== '')
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    @endif
                                </svg>
                                @if ($search !== '')
                                    <p class="text-base font-medium text-zinc-600 dark:text-zinc-400">
                                        {{ __('No posts match your search for') }} “<span class="font-semibold">{{ e($search) }}</span>”.
                                    </p>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-500">{{ __('Try different keywords or clear the search and filters.') }}</p>
                                    <flux:button variant="ghost" size="sm" wire:click="resetFilters" class="mt-4" icon="x-mark">
                                        {{ __('Clear filters') }}
                                    </flux:button>
                                @else
                                    <p class="text-base font-medium text-zinc-600 dark:text-zinc-400">{{ __('No post found.') }}</p>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-500">{{ __('Try adjusting your filters or create a new post.') }}</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 mb-2">
        {{ $posts->links(data: ['scrollTo' => false]) }}
    </div>

    <flux:modal name="delete-post" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete post?') }}</flux:heading>
                <flux:text class="mt-2">
                    <p>{{ __("You're about to delete this post.") }}</p>
                    <p>{{ __('This action cannot be reversed.') }}</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">{{ __('Delete Post') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
