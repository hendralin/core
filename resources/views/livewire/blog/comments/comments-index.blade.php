<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Comments') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Moderate and manage post comments') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <!-- Statistics Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-2.5 bg-slate-100 dark:bg-slate-700/50 rounded-lg">
                    <flux:icon.chat-bubble-left-right class="w-6 h-6 text-slate-600 dark:text-slate-300" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">{{ __('Total') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-2.5 bg-green-100 dark:bg-green-900/40 rounded-lg">
                    <flux:icon.check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $stats['approved'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">{{ __('Approved') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-2.5 bg-amber-100 dark:bg-amber-900/40 rounded-lg">
                    <flux:icon.clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $stats['pending'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">{{ __('Pending') }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-2.5 bg-red-100 dark:bg-red-900/40 rounded-lg">
                    <flux:icon.shield-exclamation class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $stats['spam'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-zinc-400">{{ __('Spam') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-4 mb-6 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    placeholder="{{ __('Search comments or author...') }}"
                    clearable
                />
            </div>
            <div class="sm:w-52">
                <flux:select wire:model.live="statusFilter">
                    <flux:select.option value="all">{{ __('All Status') }}</flux:select.option>
                    <flux:select.option value="approved">{{ __('Approved') }}</flux:select.option>
                    <flux:select.option value="pending">{{ __('Pending') }}</flux:select.option>
                    <flux:select.option value="spam">{{ __('Spam') }}</flux:select.option>
                </flux:select>
            </div>
        </div>
    </div>

    <!-- Comments List -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden shadow-sm">
        <div class="divide-y divide-gray-200 dark:divide-zinc-700">
            @forelse($comments as $comment)
                <div
                    class="p-5 hover:bg-gray-50 dark:hover:bg-zinc-700/30 transition-colors"
                    wire:key="comment-{{ $comment->id }}"
                >
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div class="flex items-start gap-4 min-w-0 flex-1">
                            <flux:avatar src="{{ $comment->user->avatar_url }}" name="{{ $comment->user->name }}" circle size="lg" />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-900 dark:text-zinc-100">
                                        {{ $comment->user->name ?? __('Unknown') }}
                                    </span>
                                    @php
                                        $statusConfig = [
                                            'approved' => ['color' => 'green', 'label' => __('Approved')],
                                            'pending' => ['color' => 'amber', 'label' => __('Pending')],
                                            'spam' => ['color' => 'red', 'label' => __('Spam')],
                                        ];
                                        $config = $statusConfig[$comment->status] ?? ['color' => 'zinc', 'label' => ucfirst($comment->status)];
                                    @endphp
                                    <flux:badge :color="$config['color']" size="xs">{{ $config['label'] }}</flux:badge>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-zinc-400 mb-2">
                                    {{ __('On') }}
                                    <a
                                        href="{{ route('blog.posts.show', $comment->post) }}"
                                        target="_blank"
                                        class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium"
                                    >
                                        {{ Str::limit($comment->post->title ?? '', 50) }}
                                    </a>
                                </p>
                                <p class="text-gray-700 dark:text-zinc-300 text-sm leading-relaxed">
                                    {{ $comment->content }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-zinc-500 mt-2">
                                    {{ $comment->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 sm:shrink-0">
                            @if($comment->status !== 'approved')
                                <flux:button
                                    variant="ghost"
                                    size="xs"
                                    wire:click="approveComment({{ $comment->id }})"
                                    icon="check-circle"
                                    class="text-green-600! dark:text-green-400!"
                                >
                                    {{ __('Approve') }}
                                </flux:button>
                            @endif
                            @if($comment->status !== 'spam')
                                <flux:button
                                    variant="ghost"
                                    size="xs"
                                    wire:click="markAsSpam({{ $comment->id }})"
                                    icon="shield-exclamation"
                                    class="text-amber-600 dark:text-amber-400!"
                                >
                                    {{ __('Spam') }}
                                </flux:button>
                            @endif
                            @if (auth()->user()->can('blog.comment.delete'))
                            <flux:modal.trigger name="delete-comment">
                                <flux:button
                                    variant="ghost"
                                    size="xs"
                                    wire:click="setCommentToDelete({{ $comment->id }})"
                                    icon="trash"
                                    class="text-red-600! dark:text-red-400!"
                                >
                                    {{ __('Delete') }}
                                </flux:button>
                            </flux:modal.trigger>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="inline-flex p-4 rounded-full bg-gray-100 dark:bg-zinc-700 mb-4">
                        <flux:icon.chat-bubble-left-right class="w-12 h-12 text-gray-400 dark:text-zinc-500" />
                    </div>
                    <flux:heading size="lg" class="text-gray-600 dark:text-zinc-400">{{ __('No comments found') }}</flux:heading>
                    <flux:text class="mt-2 text-gray-500 dark:text-zinc-500">
                        {{ $search || $statusFilter !== 'all' ? __('Try adjusting your search or filters.') : __('Comments will appear here once users start commenting on posts.') }}
                    </flux:text>
                </div>
            @endforelse
        </div>
    </div>

    @if($comments->hasPages())
        <div class="mt-6">
            {{ $comments->links(data: ['scrollTo' => false]) }}
        </div>
    @endif

    <flux:modal name="delete-comment" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete comment?') }}</flux:heading>
                <flux:text class="mt-2">
                    <p>{{ __("You're about to delete this comment.") }}</p>
                    <p>{{ __('This action cannot be reversed.') }}</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">{{ __('Delete Comment') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
