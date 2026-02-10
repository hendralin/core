<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Audit Trail Penggajian') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Riwayat perubahan data gaji') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <flux:button variant="primary" size="sm" href="{{ route('salaries.index') }}" wire:navigate icon="arrow-uturn-left" class="mb-4">Kembali</flux:button>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.document-text class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_activities'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Total Aktivitas</flux:text>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.clock class="h-8 w-8 text-green-600 dark:text-green-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['today_activities'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Hari Ini</flux:text>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.plus class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['created_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Dibuat</flux:text>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.pencil-square class="h-8 w-8 text-yellow-600 dark:text-yellow-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['updated_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Diubah</flux:text>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-4">
            <div class="flex items-center">
                <flux:icon.trash class="h-8 w-8 text-red-600 dark:text-red-400" />
                <div class="ml-3">
                    <flux:text class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['deleted_count'] }}</flux:text>
                    <flux:text class="text-xs text-gray-600 dark:text-zinc-400">Dihapus</flux:text>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:input type="text" label="Cari" wire:model.live.debounce.300ms="search" placeholder="Cari aktivitas..." clearable />
            <flux:select label="Gaji" wire:model.live="selectedSalary">
                <flux:select.option value="">Semua Gaji</flux:select.option>
                @foreach($salaryOptions as $id => $label)
                    <flux:select.option value="{{ $id }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select label="Tampilkan" wire:model.live="perPage">
                @foreach($this->perPageOptions as $opt)
                    <flux:select.option value="{{ $opt }}">{{ $opt }} per halaman</flux:select.option>
                @endforeach
            </flux:select>
            <div class="flex items-end">
                <flux:button wire:click="clearFilters" class="cursor-pointer">Reset Filter</flux:button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        @if($activities->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                @foreach($activities as $activity)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                        <div class="flex items-start space-x-4">
                            <div class="shrink-0">
                                @switch($activity->description)
                                    @case('created salary')
                                        <flux:icon.plus class="w-5 h-5 text-green-600 dark:text-green-400" />
                                        @break
                                    @case('updated salary')
                                        <flux:icon.pencil-square class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                                        @break
                                    @case('deleted salary')
                                        <flux:icon.trash class="w-5 h-5 text-red-600 dark:text-red-400" />
                                        @break
                                    @default
                                        <flux:icon.document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                @endswitch
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <flux:text class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $activity->description }}</flux:text>
                                    <flux:text class="text-xs text-gray-500 dark:text-zinc-400">{{ $activity->created_at->diffForHumans() }}</flux:text>
                                </div>
                                @if($activity->causer)
                                    <div class="mt-1 text-xs text-gray-600 dark:text-zinc-400">oleh {{ $activity->causer->name }}</div>
                                @endif
                                @if($activity->properties && isset($activity->properties['attributes']))
                                    <div class="mt-3 bg-gray-50 dark:bg-zinc-700 rounded-lg p-3 text-xs">
                                        @foreach($activity->properties['attributes'] as $field => $value)
                                            <div><span class="font-medium">{{ ucwords(str_replace('_', ' ', $field)) }}:</span> {{ $value }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-4 py-3 bg-gray-50 dark:bg-zinc-800 border-t border-gray-200 dark:border-zinc-700">
                {{ $activities->links(data: ['scrollTo' => false]) }}
            </div>
        @else
            <div class="p-12 text-center">
                <flux:icon.document-text class="mx-auto h-12 w-12 text-gray-400 dark:text-zinc-600" />
                <flux:heading size="md" class="mt-4 text-gray-900 dark:text-zinc-100">Tidak ada aktivitas</flux:heading>
                <flux:text class="mt-2 text-gray-600 dark:text-zinc-400">Belum ada riwayat perubahan data gaji.</flux:text>
            </div>
        @endif
    </div>
</div>
