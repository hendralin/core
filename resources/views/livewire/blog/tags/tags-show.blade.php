<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Blog Tag Details') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('View detailed information about this tag') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-2 mb-6">
        @can('blog.tag.edit')
            <flux:button variant="primary" size="sm" href="{{ route('blog.tags.edit', $tag->id) }}" wire:navigate icon="pencil-square" tooltip="Edit Tag">
                Edit
            </flux:button>
        @endcan

        @can('blog.tag.audit')
            <flux:button variant="ghost" size="sm" href="{{ route('blog.tags.audit', $tag->id) }}" wire:navigate icon="document-text" tooltip="View Audit Trail">
                Audit Trail
            </flux:button>
        @endcan

        <flux:button variant="ghost" size="sm" href="{{ route('blog.tags.index') }}" wire:navigate icon="arrow-left" tooltip="Back to Tags">
            Back
        </flux:button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tag Details Card -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <flux:icon.tag class="w-6 h-6 text-gray-400" />
                        <flux:heading size="lg">{{ $tag->name }}</flux:heading>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $usageStatus['status'] === 'popular' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                           ($usageStatus['status'] === 'active' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-zinc-700 dark:text-zinc-300') }}">
                        {{ ucfirst($usageStatus['status']) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:text class="font-medium text-gray-900 dark:text-zinc-100">Slug</flux:text>
                        <flux:text class="text-gray-600 dark:text-zinc-300"><code class="bg-gray-100 dark:bg-zinc-700 px-2 py-1 rounded text-sm">{{ $tag->slug }}</code></flux:text>
                    </div>
                </div>
            </div>

            <!-- Recent Posts -->
            @if($recentPosts['posts']->count() > 0)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="md" class="mb-4">{{ __('Recent Posts') }}</flux:heading>
                    <div class="space-y-3">
                        @foreach($recentPosts['posts'] as $post)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                                <div class="flex-1">
                                    <flux:text class="font-medium text-gray-900 dark:text-zinc-100">{{ $post->title }}</flux:text>
                                    <flux:text class="text-sm text-gray-600 dark:text-zinc-300">{{ $post->created_at->format('M j, Y') }}</flux:text>
                                </div>
                                @can('blog.post.view')
                                    <flux:button variant="ghost" size="xs" href="#" wire:navigate>
                                        <flux:icon.eye class="w-4 h-4" />
                                    </flux:button>
                                @endcan
                            </div>
                        @endforeach

                        @if($recentPosts['has_more'])
                            <div class="text-center pt-2">
                                <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                                    And {{ $recentPosts['remaining_count'] }} more posts...
                                </flux:text>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics Card -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="md" class="mb-4">{{ __('Statistics') }}</flux:heading>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <flux:text class="text-gray-600 dark:text-zinc-300">{{ __('Total Posts') }}</flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-zinc-100">{{ $statistics['posts_count'] }}</flux:text>
                    </div>
                    <div class="flex items-center justify-between">
                        <flux:text class="text-gray-600 dark:text-zinc-300">{{ __('Created') }}</flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-zinc-100">{{ $statistics['created_at']->format('M j, Y') }}</flux:text>
                    </div>
                    <div class="flex items-center justify-between">
                        <flux:text class="text-gray-600 dark:text-zinc-300">{{ __('Last Updated') }}</flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-zinc-100">{{ $statistics['updated_at']->format('M j, Y') }}</flux:text>
                    </div>
                </div>
            </div>

            <!-- Usage Status Card -->
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                <flux:heading size="md" class="mb-4">{{ __('Usage Status') }}</flux:heading>
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full
                            {{ $usageStatus['status_color'] === 'green' ? 'bg-green-500' :
                               ($usageStatus['status_color'] === 'blue' ? 'bg-blue-500' : 'bg-gray-500') }}"></div>
                        <flux:text class="text-gray-600 dark:text-zinc-300 capitalize">{{ $usageStatus['status'] }}</flux:text>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">
                        @if($usageStatus['is_used'])
                            This tag is actively used with {{ $statistics['posts_count'] }} posts.
                        @else
                            This tag has no posts yet.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
