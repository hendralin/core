<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Templates') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all your templates') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        @session('success')
            <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
        @endsession

        @session('error')
            <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
        @endsession

        <!-- Search & Filters -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 mb-6">
            <div class="p-4 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                <div class="space-y-4 lg:space-y-0 lg:flex lg:flex-wrap lg:items-center lg:justify-between lg:gap-4">
                    <!-- Search -->
                    <div class="w-full lg:flex-1 lg:max-w-md">
                        <div class="relative">
                            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search templates..." clearable />
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-center">
                        <!-- Session Filter -->
                        <div class="flex items-center">
                            <label for="session-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2 min-w-fit">Session:</label>
                            <flux:select wire:model.live="selectedSession" class="min-w-32">
                                <flux:select.option value="">All Sessions</flux:select.option>
                                @foreach($sessions as $session)
                                    <flux:select.option value="{{ $session->id }}">{{ $session->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center">
                            <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2 min-w-fit">Status:</label>
                            <flux:select wire:model.live="statusFilter" class="min-w-24">
                                <flux:select.option value="">All Status</flux:select.option>
                                <flux:select.option value="active">Active</flux:select.option>
                                <flux:select.option value="inactive">Inactive</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Per Page Filter -->
                        <div class="flex items-center">
                            <label for="per-page" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mr-2 min-w-fit">Show:</label>
                            <flux:select wire:model.live="perPage" class="w-20">
                                <flux:select.option value="10">10</flux:select.option>
                                <flux:select.option value="25">25</flux:select.option>
                                <flux:select.option value="50">50</flux:select.option>
                                <flux:select.option value="100">100</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Clear Filters -->
                        @if($search || $statusFilter || $selectedSession)
                            <div class="flex justify-start sm:justify-end">
                                <flux:button wire:click="clearFilters" variant="ghost" class="cursor-pointer" tooltip="Clear Filters">
                                    Clear Filters
                                </flux:button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="p-4 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex flex-wrap gap-2">
                    @can('template.create')
                        <flux:button variant="primary" href="{{ route('templates.create') }}" size="sm" wire:navigate icon="plus" tooltip="Create Template">Create</flux:button>
                    @endcan

                    @can('template.audit')
                        <flux:button variant="ghost" href="{{ route('templates.audit') }}" size="sm" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
                    @endcan

                    <div wire:loading>
                        <flux:icon.loading class="text-red-600" />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="text-xs text-gray-700 bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                No.
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('name')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-gray-300">
                                    <span>Name</span>
                                    @if($sortField === 'name')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Session</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Usage Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Last Used</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-gray-300">
                                    <span>Created</span>
                                    @if($sortField === 'created_at')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                                <button wire:click="sortBy('updated_at')" class="flex items-center space-x-1 cursor-pointer uppercase hover:text-gray-700 dark:hover:text-gray-300">
                                    <span>Updated</span>
                                    @if($sortField === 'updated_at')
                                        <flux:icon.chevron-up class="h-4 w-4 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                        <flux:icon.chevron-down class="h-4 w-4 -mt-2 {{ $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                        @if(isset($templates) && $templates->count() > 0)
                            @foreach($templates as $index => $template)
                                <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400 text-center">
                                        {{ $templates->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $template->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-zinc-400">{{ Str::limit($template->body, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        @if($template->wahaSession)
                                            {{ $template->wahaSession->name }}
                                        @else
                                            <span class="text-gray-400">No session</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $template->usage_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        @if($template->last_used_at)
                                            {{ $template->last_used_at->diffForHumans() }}
                                        @else
                                            <span class="text-gray-400">Never</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($template->is_active)
                                            <flux:badge color="green">Active</flux:badge>
                                        @else
                                            <flux:badge color="red">Inactive</flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $template->created_at->format('M d, Y') }}
                                        <div class="text-xs text-gray-500 dark:text-zinc-500">
                                            by {{ $template->createdBy->name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $template->updated_at->format('M d, Y') }}
                                        <div class="text-xs text-gray-500 dark:text-zinc-500">
                                            by {{ $template->updatedBy->name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            @can('template.view')
                                                <flux:button variant="ghost" size="xs" square href="{{ route('templates.show', $template->id) }}" wire:navigate tooltip="Show">
                                                    <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                                </flux:button>
                                            @endcan

                                            @can('template.edit')
                                                <flux:button variant="ghost" size="xs" square href="{{ route('templates.edit', $template->id) }}" wire:navigate tooltip="Edit">
                                                    <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                                </flux:button>
                                            @endcan

                                            @can('template.view')
                                                <flux:modal.trigger name="preview-template">
                                                    <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setTemplateToPreview({{ $template->id }})" tooltip="Preview Template">
                                                        <flux:icon.eye variant="mini" class="text-blue-500 dark:text-blue-300" />
                                                    </flux:button>
                                                </flux:modal.trigger>
                                            @endcan

                                            @can('template.delete')
                                                <flux:modal.trigger name="delete-template">
                                                    <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setTemplateToDelete({{ $template->id }})" tooltip="Delete">
                                                        <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                                    </flux:button>
                                                </flux:modal.trigger>
                                            @endcan

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-zinc-400">
                                    @if(isset($search) && !empty($search))
                                        No results found for "{{ $search }}"
                                    @else
                                        No data available in table
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($templates) && $templates->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
                    {{ $templates->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>

    <flux:modal name="preview-template" class="min-w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Template Preview</flux:heading>
                <flux:text class="text-sm text-gray-600 dark:text-zinc-400 mt-1">How the message will appear</flux:text>
            </div>

            @if($templateToPreview)
                <div class="space-y-4">
                    <!-- Template Info -->
                    <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <flux:heading size="md">{{ $templateToPreview->name }}</flux:heading>
                            @if ($templateToPreview->is_active)
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            @else
                                <flux:badge color="red" size="sm">Inactive</flux:badge>
                            @endif
                        </div>
                        <div class="text-sm text-gray-600 dark:text-zinc-400">
                            Usage: {{ $templateToPreview->usage_count }} |
                            @if($templateToPreview->last_used_at)
                                Last used: {{ $templateToPreview->last_used_at->diffForHumans() }}
                            @else
                                Never used
                            @endif
                        </div>
                    </div>

                    <!-- Message Preview -->
                    <div class="bg-green-50 dark:bg-green-900/10 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <flux:heading size="sm" class="mb-3 text-green-800 dark:text-green-200">Message Preview</flux:heading>

                        <!-- WhatsApp-like message bubble -->
                        <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3 max-w-sm ml-auto">
                            <div class="text-sm text-gray-900 dark:text-zinc-100">
                                @if($templateToPreview->header)
                                    <div class="font-semibold mb-2">{{ $templateToPreview->header }}</div>
                                @endif
                                <div class="whitespace-pre-wrap">{{ $templateToPreview->body }}</div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-zinc-400 mt-2 text-right">
                                12:34 PM ✓✓
                            </div>
                        </div>

                        <!-- Additional info -->
                        <div class="text-xs text-gray-600 dark:text-zinc-400 mt-3 space-y-1">
                            <div>• Template variables: @{{1}}, @{{2}}, etc.</div>
                            <div>• Supports formatting: *bold*, _italic_</div>
                            <div>• Character count: {{ strlen($templateToPreview->body) }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="delete-template" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete template?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this template.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">Delete Template</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Download Script -->
    <script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('download-file', (data) => {
            const link = document.createElement('a');
            link.href = data.url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
    </script>
</div>
