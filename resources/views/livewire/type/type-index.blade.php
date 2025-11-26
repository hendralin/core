<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Types') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all your types') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <div class="space-y-4 mb-2">
        <!-- Actions Section -->
        <div class="flex flex-wrap gap-2">
            @can('type.create')
                <flux:button variant="primary" size="sm" href="{{ route('types.create') }}" wire:navigate icon="plus" tooltip="Tambah Tipe">Tambah</flux:button>
            @endcan

            @can('type.audit')
                <flux:button variant="ghost" size="sm" href="{{ route('types.audit') }}" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
            @endcan

            <!-- Export Actions -->
            <div class="flex gap-1">
                <flux:button variant="ghost" size="sm" wire:click="exportExcel" icon="document-arrow-down" tooltip="Export to Excel" class="cursor-pointer">
                    Excel
                </flux:button>
                <flux:button variant="ghost" size="sm" wire:click="exportPdf" icon="document-arrow-down" tooltip="Export to PDF" class="cursor-pointer">
                    PDF
                </flux:button>
            </div>

            <div wire:loading>
                <flux:icon.loading class="text-red-600" />
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="space-y-4 mb-3 mt-4">
        <!-- Primary Filters -->
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
            <div class="flex items-center gap-2">
                <label for="brand-filter" class="text-sm font-medium text-gray-700 dark:text-zinc-300">Brand:</label>
                <flux:select wire:model.live="selectedBrand" size="sm">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Clear Filters Button -->
            @if(!empty($search) || !empty($selectedBrand))
                <flux:button size="sm" wire:click="clearFilters" icon="x-mark" class="cursor-pointer">
                    Clear Filters
                </flux:button>
            @endif
        </div>

        <!-- Secondary Filters -->
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
            <div class="flex items-center">
                <label for="search" class="text-sm text-gray-700 dark:text-zinc-300 mr-2">Search:</label>
                <flux:input wire:model.live.debounce.500ms="search" id="search" placeholder="Types..." clearable />
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 border dark:border-zinc-700 dark:text-zinc-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">No.</th>
                    <th scope="col" class="px-4 py-3 w-32">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'brand.name') {{ $sortDirection }} @endif" wire:click="sortBy('brand.name')">
                            Brand
                            @if ($sortField == 'brand.name' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'brand.name' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-60">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'name') {{ $sortDirection }} @endif" wire:click="sortBy('name')">
                            Name
                            @if ($sortField == 'name' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'name' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-20">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'vehicles_count') {{ $sortDirection }} @endif" wire:click="sortBy('vehicles_count')">
                            Vehicles
                            @if ($sortField == 'vehicles_count' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'vehicles_count' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'description') {{ $sortDirection }} @endif" wire:click="sortBy('description')">
                            Description
                            @if ($sortField == 'description' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'description' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-32">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'created_at') {{ $sortDirection }} @endif" wire:click="sortBy('created_at')">
                            Created
                            @if ($sortField == 'created_at' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'created_at' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-1/12">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($types) && $types->count() > 0)
                    @foreach($types as $index => $type)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-white">{{ $types->firstItem() + $index }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="text-gray-600 dark:text-zinc-300">{{ $type->brand->name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 dark:text-zinc-300">{{ $type->name }}</span>
                                    @if($type->created_at->diffInDays() <= 7)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            New
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($type->vehicles_count > 0)
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else
                                        bg-gray-100 text-gray-800 dark:bg-zinc-900 dark:text-zinc-300
                                    @endif">
                                    {{ $type->vehicles_count }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap lg:whitespace-normal text-gray-600 dark:text-zinc-300">{!! nl2br(e($type->description)) !!}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-zinc-400">
                                {{ $type->created_at->format('M d, Y') }}
                                <div class="text-gray-400 dark:text-zinc-500">{{ $type->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @can('type.view')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('types.show', $type->id) }}" wire:navigate tooltip="Show">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                    </flux:button>
                                @endcan

                                @can('type.edit')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('types.edit', $type->id) }}" wire:navigate tooltip="Edit">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>
                                @endcan

                                @can('type.delete')
                                    <flux:modal.trigger name="delete-type">
                                        <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setTypeToDelete({{ $type->id }})" tooltip="Delete">
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
                            @elseif(isset($selectedBrand) && !empty($selectedBrand))
                                No types found for selected brand
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
        {{ $types->links(data: ['scrollTo' => false]) }}
    </div>

    <flux:modal name="delete-type" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete type?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this type.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">Delete Type</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
