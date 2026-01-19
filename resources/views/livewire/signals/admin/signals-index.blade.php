<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Stock Signals') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Kelola sinyal saham untuk investor') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Alert Messages -->
    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <!-- Actions Bar -->
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <flux:button variant="primary" size="sm" href="{{ route('admin.signals.create') }}" wire:navigate icon="plus">
            {{ __('Tambah Sinyal Manual') }}
        </flux:button>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mt-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
            <!-- Per Page Selector -->
            <flux:select wire:model.live="perPage" label="{{ __('Per Page') }}">
                @foreach ($this->perPageOptions as $option)
                <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>

            <!-- Search Input -->
            <div class="flex-1 min-w-0">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    label="{{ __('Search') }}"
                    placeholder="{{ __('Kode emiten atau rekomendasi...') }}"
                    icon="magnifying-glass"
                    clearable />
            </div>

            <!-- Signal Type Filter -->
            <div class="w-full lg:w-48">
                <flux:select wire:model.live="signalType" label="{{ __('Tipe Sinyal') }}">
                    <flux:select.option value="">{{ __('Semua Tipe') }}</flux:select.option>
                    @foreach($signalTypes as $type)
                        <flux:select.option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Status Filter -->
            <div class="w-full lg:w-48">
                <flux:select wire:model.live="status" label="{{ __('Status') }}">
                    <flux:select.option value="">{{ __('Semua Status') }}</flux:select.option>
                    <flux:select.option value="draft">Draft</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="published">Published</flux:select.option>
                    <flux:select.option value="expired">Expired</flux:select.option>
                    <flux:select.option value="cancelled">Cancelled</flux:select.option>
                </flux:select>
            </div>

            <!-- Column Visibility -->
            <div class="shrink-0">
                <flux:dropdown>
                    <flux:button icon="view-columns">
                        {{ __('Columns') }}
                        <flux:badge size="sm" color="blue" class="ml-1">{{ $this->visibleColumnCount }}</flux:badge>
                    </flux:button>

                    <flux:menu class="w-64 max-h-80 overflow-y-auto">
                        <flux:menu.heading>{{ __('Toggle Columns') }}</flux:menu.heading>

                        <!-- Select All / Deselect All -->
                        @if($this->isAllColumnsSelected)
                            <flux:menu.item wire:click="deselectAllColumns">
                                <div class="flex items-center justify-between w-full font-medium">
                                    <span>{{ __('Deselect All') }}</span>
                                    <flux:icon.x-mark class="size-4 text-red-500" />
                                </div>
                            </flux:menu.item>
                        @else
                            <flux:menu.item wire:click="selectAllColumns">
                                <div class="flex items-center justify-between w-full font-medium">
                                    <span>{{ __('Select All') }}</span>
                                    <flux:icon.check-circle class="size-4 text-green-500" />
                                </div>
                            </flux:menu.item>
                        @endif

                        <flux:menu.separator />

                        @foreach($availableColumns as $column => $config)
                            <flux:menu.item wire:click="toggleColumn('{{ $column }}')">
                                <div class="flex items-center justify-between w-full">
                                    <span>{{ __($config['label']) }}</span>
                                    @if(in_array($column, $visibleColumns))
                                        <flux:icon.check class="size-4 text-green-500" />
                                    @endif
                                </div>
                            </flux:menu.item>
                        @endforeach

                        <flux:menu.separator />

                        <flux:menu.item wire:click="resetColumns">
                            <div class="flex items-center text-blue-600 dark:text-blue-400">
                                <flux:icon.arrow-path class="size-4 mr-2" />
                                {{ __('Reset to Default') }}
                            </div>
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>

            <div class="flex items-center gap-2">
                <!-- Clear Filters Button -->
                <div class="shrink-0">
                    <flux:button variant="ghost" wire:click="clearFilters" icon="x-mark">
                        {{ __('Clear') }}
                    </flux:button>
                </div>

                <!-- Loading Indicator -->
                <div class="hidden md:block">
                    <div wire:loading class="shrink-0">
                        <flux:icon.loading class="text-blue-600 size-4 mt-2" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden mb-6">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:border-zinc-700 dark:bg-zinc-800/80">
                    <tr>
                        @foreach($availableColumns as $column => $config)
                            @if(in_array($column, $visibleColumns))
                                <th class="px-4 py-3 text-{{ $config['align'] }} text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap {{ $column === 'no' ? 'w-16' : '' }}">
                                    @if($config['sortable'] && $column !== 'no')
                                        <div class="flex items-center {{ $config['align'] === 'right' ? 'justify-end' : '' }} cursor-pointer" wire:click="sortBy('{{ $column }}')">
                                            {{ __($config['label']) }}
                                            @if($sortField === $column)
                                                <flux:icon.chevron-up class="size-4 ml-1 {{ $sortDirection === 'desc' ? 'rotate-180' : '' }}" />
                                            @else
                                                <flux:icon.chevron-up-down class="size-4 ml-1" />
                                            @endif
                                        </div>
                                    @else
                                        {{ __($config['label']) }}
                                    @endif
                                </th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $startNumber = $isAllData ? 1 : (($signals->currentPage() - 1) * $signals->perPage() + 1);
                    @endphp
                    @forelse($isAllData ? $signals : $signals->items() as $index => $signal)
                        <tr wire:loading.class="opacity-50" class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 hover:bg-gray-100 dark:border-zinc-700 dark:hover:bg-zinc-700/50">
                            @if(in_array('no', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                    {{ $startNumber + $index }}
                                </td>
                            @endif

                            @if(in_array('kode_emiten', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="relative w-10 h-10 shrink-0"
                                        x-data="{ imageLoaded: false, imageError: false }"
                                        x-init="imageLoaded = false; imageError = false"
                                        wire:key="stock-logo-{{ $signal->kode_emiten }}"
                                    >
                                        {{-- Fallback initials --}}
                                        <div
                                            class="absolute inset-0 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center"
                                            x-show="!imageLoaded || imageError"
                                            x-cloak
                                        >
                                            <span class="text-gray-600 dark:text-zinc-400 font-bold text-xs">{{ substr($signal->kode_emiten, 0, 2) }}</span>
                                        </div>
                                        {{-- Logo image --}}
                                        @if($signal->stockCompany && $signal->stockCompany->logo_url)
                                            <img
                                                src="{{ $signal->stockCompany->logo_url }}"
                                                alt="{{ $signal->kode_emiten }}"
                                                class="absolute inset-0 w-10 h-10 rounded-full object-contain bg-white dark:bg-zinc-800 p-0.5"
                                                x-show="imageLoaded && !imageError"
                                                x-cloak
                                                x-on:load="imageLoaded = true"
                                                x-on:error="imageError = true"
                                            />
                                        @endif
                                    </div>
                                    <div class="flex flex-col ml-3">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <a href="{{ route('admin.signals.show', $signal) }}" wire:navigate class="hover:no-underline">{{ $signal->kode_emiten }}</a>
                                            </div>
                                            @if($signal->created_at->diffInDays(now()) < 4)
                                                <flux:badge color="red" size="sm" class="text-xs animate-pulse">
                                                    New
                                                </flux:badge>
                                            @endif
                                        </div>
                                        @if($signal->stockCompany)
                                            <div class="text-sm text-gray-500 dark:text-zinc-400">
                                                {{ $signal->stockCompany->nama_emiten ?? '' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            @endif

                            @if(in_array('id', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                                    {{ $signal->id }}
                                </td>
                            @endif

                            @if(in_array('signal_type', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($signal->signal_type === 'value_breakthrough' && is_null($signal->user_id))
                                        <flux:badge color="green" size="sm">
                                            <svg class="size-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            Auto-generated
                                        </flux:badge>
                                    @else
                                        <flux:badge color="blue" size="sm">
                                            {{ ucfirst(str_replace('_', ' ', $signal->signal_type)) }}
                                        </flux:badge>
                                    @endif
                                </div>
                            </td>
                            @endif

                            @if(in_array('market_cap', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->formatted_market_cap }}
                                </td>
                            @endif

                            @if(in_array('pbv', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->formatted_pbv }}
                                </td>
                            @endif

                            @if(in_array('per', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->formatted_per }}
                                </td>
                            @endif

                            {{-- Before Data (H-1) --}}
                            @if(in_array('before_date', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">
                                    {{ $signal->before_date ? $signal->before_date->format('d/m/Y') : '-' }}
                                </td>
                            @endif

                            @if(in_array('before_close', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->before_close ? number_format($signal->before_close, 0, ',', '.') : '-' }}
                                </td>
                            @endif

                            @if(in_array('before_volume', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->formatted_before_volume }}
                                </td>
                            @endif

                             @if(in_array('before_value', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->formatted_before_value }}
                                </td>
                            @endif

                            {{-- Hit Data (H) --}}
                            @if(in_array('hit_date', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">
                                    {{ $signal->hit_date ? $signal->hit_date->format('d/m/Y') : '-' }}
                                </td>
                            @endif

                            @if(in_array('hit_close', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->hit_close ? number_format($signal->hit_close, 0, ',', '.') : '-' }}
                                </td>
                            @endif

                            @if(in_array('hit_volume', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->formatted_hit_volume }}
                                </td>
                            @endif

                            @if(in_array('hit_value', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->formatted_hit_value }}
                                </td>
                            @endif

                            {{-- After Data (H+1) --}}
                            @if(in_array('after_date', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">
                                    {{ $signal->after_date ? $signal->after_date->format('d/m/Y') : '-' }}
                                </td>
                            @endif

                            @if(in_array('after_close', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->after_close ? number_format($signal->after_close, 0, ',', '.') : '-' }}
                                </td>
                            @endif

                            @if(in_array('after_volume', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->formatted_after_volume }}
                                </td>
                            @endif

                            @if(in_array('after_value', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $signal->formatted_after_value }}
                                </td>
                            @endif

                            {{-- Management --}}
                            @if(in_array('status', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap">
                                @php
                                    $statusColor = match($signal->status) {
                                        'draft' => 'gray',
                                        'active' => 'blue',
                                        'published' => 'green',
                                        'expired' => 'yellow',
                                        'cancelled' => 'red',
                                        default => 'gray'
                                    };
                                @endphp
                                <flux:badge color="{{ $statusColor }}" size="sm">
                                    {{ $signal->status_label }}
                                </flux:badge>
                                @if($signal->published_at)
                                    <div class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                                        {{ $signal->published_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            @endif

                            @if(in_array('published_at', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">
                                    {{ $signal->published_at ? $signal->published_at->format('d/m/Y H:i') : '-' }}
                                </td>
                            @endif

                            @if(in_array('notes', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $signal->notes }}">
                                    {{ $signal->notes ? Str::limit($signal->notes, 50) : '-' }}
                                </td>
                            @endif

                            @if(in_array('recommendation', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $signal->recommendation }}">
                                    {{ $signal->recommendation ? Str::limit($signal->recommendation, 50) : '-' }}
                                </td>
                            @endif

                            @if(in_array('user_id', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">
                                    {{ $signal->user_id ?? '-' }}
                                </td>
                            @endif

                            @if(in_array('created_by', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($signal->user)
                                        {{ $signal->user->name }}
                                    @else
                                        <span class="text-gray-500 dark:text-zinc-400">-</span>
                                    @endif
                                </td>
                            @endif

                            @if(in_array('created_at', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $signal->created_at->format('d/m/Y H:i') }}
                                </td>
                            @endif

                            @if(in_array('updated_at', $visibleColumns))
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">
                                    {{ $signal->updated_at->format('d/m/Y H:i') }}
                                </td>
                            @endif

                            @if(in_array('actions', $visibleColumns))
                                <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <flux:button variant="ghost" size="xs" square href="{{ route('admin.signals.show', $signal) }}" wire:navigate tooltip="{{ __('Lihat Detail') }}">
                                        <flux:icon.eye variant="mini" class="text-blue-500 dark:text-blue-300" />
                                    </flux:button>

                                    <flux:button variant="ghost" size="xs" square href="{{ route('admin.signals.edit', $signal) }}" wire:navigate tooltip="{{ __('Edit') }}">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>

                                    @if($signal->status !== 'published')
                                        <flux:button variant="ghost" size="xs" square wire:click="publish({{ $signal->id }})" tooltip="{{ __('Publikasikan') }}">
                                            <flux:icon.check-circle variant="mini" class="text-green-500 dark:text-green-300" />
                                        </flux:button>
                                    @endif

                                    @if($signal->status !== 'cancelled')
                                        <flux:button variant="ghost" size="xs" square wire:click="cancel({{ $signal->id }})" tooltip="{{ __('Batalkan') }}">
                                            <flux:icon.x-circle variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    @endif

                                    @if($signal->signal_type !== 'value_breakthrough' || !is_null($signal->user_id))
                                        <flux:button variant="ghost" size="xs" square wire:click="delete({{ $signal->id }})"
                                                     wire:confirm="{{ __('Apakah Anda yakin ingin menghapus sinyal ini?') }}"
                                                     tooltip="{{ __('Hapus') }}">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($visibleColumns) }}" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <flux:icon.inbox class="size-12 text-gray-400 dark:text-zinc-500 mb-4" />
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('Tidak ada sinyal yang ditemukan') }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400 mb-4">{{ __('Belum ada sinyal saham yang sesuai dengan filter Anda.') }}</p>
                                    @if($search || $signalType || $status)
                                        <flux:button variant="outline" size="sm" href="{{ route('admin.signals.index') }}" wire:navigate>
                                            {{ __('Reset Filter') }}
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                    </table>
            </table>
        </div>

        <!-- Pagination -->
        @if(!$isAllData && $signals->hasPages())
            <div class="bg-gray-50 dark:bg-zinc-800/50 px-4 py-3 border-t border-gray-200 dark:border-zinc-700">
                {{ $signals->links(data: ['scrollTo' => false]) }}
            </div>
        @elseif($isAllData)
            <div class="bg-gray-50 dark:bg-zinc-800/50 px-4 py-3 border-t border-gray-200 dark:border-zinc-700">
                <p class="text-sm text-gray-600 dark:text-zinc-400">
                    {{ __('Showing all') }} <span class="font-medium">{{ $signals->count() }}</span> {{ __('results') }}
                </p>
            </div>
        @endif
    </div>
</div>
