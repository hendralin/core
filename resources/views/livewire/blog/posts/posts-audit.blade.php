<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Post Audit Trail') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Track all changes made to the post: ') }}"{{ $post->title }}"
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-2 mb-6">
        @can('blog.post.view')
            <flux:button variant="ghost" size="sm" href="{{ route('blog.posts.show', $post) }}" wire:navigate icon="eye" tooltip="{{ __('View Post') }}">
                {{ __('View Post') }}
            </flux:button>
        @endcan
        <flux:button variant="ghost" size="sm" href="{{ route('blog.posts.index') }}" wire:navigate icon="arrow-left" tooltip="{{ __('Back to Posts') }}">
            {{ __('Back to Posts') }}
        </flux:button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <flux:input
                    wire:model.live.debounce.500ms="search"
                    icon="magnifying-glass"
                    placeholder="{{ __('Search audit trail...') }}"
                    clearable
                />
            </div>
            <div class="flex items-center gap-2">
                <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">{{ __('Show:') }}</label>
                <flux:select id="per-page" wire:model.live="perPage">
                    @foreach ($this->perPageOptions as $option)
                        <flux:select.option value="{{ $option }}">{{ is_string($option) ? __('All') : $option }}</flux:select.option>
                    @endforeach
                </flux:select>
                <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300">{{ __('entries') }}</label>
            </div>
        </div>
    </div>

    <!-- Audit Trail Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-225 text-sm text-left text-gray-500 dark:text-zinc-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-zinc-700 dark:text-zinc-400">
                    <tr>
                        <th scope="col" class="px-4 py-3 w-40">{{ __('Date & Time') }}</th>
                        <th scope="col" class="px-4 py-3 w-24">{{ __('Action') }}</th>
                        <th scope="col" class="px-4 py-3 w-48">{{ __('User') }}</th>
                        <th scope="col" class="px-4 py-3">{{ __('Changes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr class="border-b dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-900 dark:text-white">
                                {{ $activity->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $activity->event === 'created' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                       ($activity->event === 'updated' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                       'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                    {{ ucfirst($activity->event) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">
                                {{ $activity->causer ? $activity->causer->name : __('System') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($activity->event === 'created')
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-zinc-100 mb-1">{{ __('Post created with:') }}</div>
                                        @if($activity->properties && isset($activity->properties['attributes']))
                                            <ul class="text-xs text-gray-600 dark:text-zinc-300 space-y-1">
                                                @foreach($activity->properties['attributes'] as $key => $value)
                                                    <li>• <strong>{{ ucfirst($key) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @elseif($activity->event === 'updated')
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-zinc-100 mb-1">{{ __('Fields updated:') }}</div>
                                        @if($activity->properties && isset($activity->properties['old']) && isset($activity->properties['attributes']))
                                            <ul class="text-xs space-y-1">
                                                @foreach($activity->properties['attributes'] as $key => $newValue)
                                                    @php $oldValue = $activity->properties['old'][$key] ?? null; @endphp
                                                    @if($oldValue !== $newValue)
                                                        <li class="text-gray-600 dark:text-zinc-300">
                                                            • <strong>{{ ucfirst($key) }}:</strong>
                                                            <span class="text-red-600 dark:text-red-400">{{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</span>
                                                            →
                                                            <span class="text-green-600 dark:text-green-400">{{ is_array($newValue) ? json_encode($newValue) : $newValue }}</span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @elseif($activity->event === 'deleted')
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-zinc-100 mb-1">{{ __('Post deleted with data:') }}</div>
                                        @if($activity->properties && isset($activity->properties['attributes']))
                                            <ul class="text-xs text-gray-600 dark:text-zinc-300 space-y-1">
                                                @foreach($activity->properties['attributes'] as $key => $value)
                                                    <li>• <strong>{{ ucfirst($key) }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-sm text-gray-600 dark:text-zinc-300">
                                        {{ $activity->description ?? __('Unknown action') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-400">
                                @if(!empty($search))
                                    {{ __('No audit records found for') }} "{{ $search }}"
                                @else
                                    {{ __('No audit records found for this post') }}
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-700">
            {{ $activities->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

    <!-- Summary Statistics -->
    @if($activities->total() > 0)
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
                <div class="text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $activities->total() }}</div>
                <div class="text-sm text-gray-600 dark:text-zinc-400">{{ __('Total Activities') }}</div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $activities->filter(fn($a) => $a->event === 'created')->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-zinc-400">{{ __('Created') }}</div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $activities->filter(fn($a) => $a->event === 'updated')->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-zinc-400">{{ __('Updated') }}</div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $activities->filter(fn($a) => $a->event === 'deleted')->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-zinc-400">{{ __('Deleted') }}</div>
            </div>
        </div>
    @endif
</div>
