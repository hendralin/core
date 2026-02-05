<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Post Details') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('View post information') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @if (auth()->user()->can('blog.post.edit.all') || (auth()->user()->can('blog.post.edit.own') && $post->user_id === auth()->id()))
            <flux:button variant="primary" size="sm" href="{{ route('blog.posts.edit', $post) }}" wire:navigate icon="pencil-square" tooltip="{{ __('Edit Post') }}">
                {{ __('Edit') }}
            </flux:button>
        @endif

        @can('blog.post.audit')
            <flux:button variant="ghost" size="sm" href="{{ route('blog.posts.audit', $post) }}" wire:navigate icon="document-text" tooltip="{{ __('Audit Trail') }}">
                {{ __('Audit Trail') }}
            </flux:button>
        @endcan

        <flux:button variant="ghost" size="sm" href="{{ route('blog.posts.index') }}" wire:navigate icon="arrow-left" tooltip="{{ __('Back to Posts') }}">
            {{ __('Back') }}
        </flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Featured Image --}}
            @if ($post->featured_image)
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden bg-white dark:bg-zinc-800">
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full h-64 object-cover">
                </div>
            @endif

            {{-- Post header card --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                    <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100">{{ $post->title }}</flux:heading>
                    @if ($post->status === 'published')
                        <flux:badge size="sm" color="green">{{ ucfirst($post->status) }}</flux:badge>
                    @elseif ($post->status === 'draft')
                        <flux:badge size="sm" color="amber">{{ ucfirst($post->status) }}</flux:badge>
                    @else
                        <flux:badge size="sm" color="zinc">{{ ucfirst($post->status) }}</flux:badge>
                    @endif
                </div>

                <div class="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                    <flux:avatar src="{{ $post->user->avatar_url }}" name="{{ $post->user->name }}" size="sm" />
                    <div>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $post->user->name }}</span>
                        <span class="text-zinc-500 dark:text-zinc-500"> · {{ $post->created_at->format('M d, Y') }}</span>
                        @if ($post->published_at)
                            <span class="text-zinc-500 dark:text-zinc-500"> · {{ __('Published') }} {{ $post->published_at->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>

                @if ($post->excerpt)
                    <flux:text class="text-zinc-600 dark:text-zinc-300 border-l-2 border-zinc-300 dark:border-zinc-600 pl-4 my-4">
                        {{ $post->excerpt }}
                    </flux:text>
                @endif

                {{-- Categories & Tags --}}
                <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    @if ($post->categories->isNotEmpty())
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Categories') }}:</span>
                            @foreach ($post->categories as $category)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                    style="background-color: {{ $category->color }}20; color: {{ $category->color }};"
                                >
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    @if ($post->tags->isNotEmpty())
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ __('Tags') }}:</span>
                            @foreach ($post->tags as $tag)
                                <flux:badge size="sm" variant="subtle">#{{ $tag->name }}</flux:badge>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Content --}}
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="md" class="mb-4">{{ __('Content') }}</flux:heading>
                <div class="prose prose-zinc dark:prose-invert max-w-none prose-headings:text-zinc-900 dark:prose-headings:text-zinc-100 prose-p:text-zinc-600 dark:prose-p:text-zinc-300">
                    {!! $post->content !!}
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="md" class="mb-4">{{ __('Statistics') }}</flux:heading>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <flux:text class="text-zinc-600 dark:text-zinc-400">{{ __('Comments') }}</flux:text>
                        <flux:text class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $post->comments_count }}</flux:text>
                    </div>
                    <div class="flex items-center justify-between">
                        <flux:text class="text-zinc-600 dark:text-zinc-400">{{ __('Created') }}</flux:text>
                        <flux:text class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $post->created_at->format('M j, Y H:i') }}</flux:text>
                    </div>
                    <div class="flex items-center justify-between">
                        <flux:text class="text-zinc-600 dark:text-zinc-400">{{ __('Last updated') }}</flux:text>
                        <flux:text class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $post->updated_at->format('M j, Y H:i') }}</flux:text>
                    </div>
                    @if ($post->published_at)
                        <div class="flex items-center justify-between">
                            <flux:text class="text-zinc-600 dark:text-zinc-400">{{ __('Published at') }}</flux:text>
                            <flux:text class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $post->published_at->format('M j, Y H:i') }}</flux:text>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                <flux:heading size="md" class="mb-4">{{ __('Slug') }}</flux:heading>
                <code class="block text-sm bg-zinc-100 dark:bg-zinc-700 px-3 py-2 rounded text-zinc-800 dark:text-zinc-200 break-all">{{ $post->slug }}</code>
            </div>
        </div>
    </div>
</div>
