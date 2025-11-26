<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Vendors') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all your vendors') }}</flux:subheading>
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
            @can('vendor.create')
                <flux:button variant="primary" size="sm" href="{{ route('vendors.create') }}" wire:navigate icon="plus" tooltip="Tambah Vendor">Tambah</flux:button>
            @endcan

            @can('vendor.audit')
                <flux:button variant="ghost" size="sm" href="{{ route('vendors.audit') }}" wire:navigate icon="document-text" tooltip="Audit Trail">Audit</flux:button>
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
            <label for="per-page" class="text-sm text-gray-700 dark:text-zinc-300 mr-2">Search:</label>
            <flux:input wire:model.live.debounce.500ms="search" placeholder="Vendors..." clearable />
        </div>
    </div>

    <!-- Table Section -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 border dark:border-zinc-700 dark:text-zinc-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b dark:border-b-0 dark:bg-zinc-700 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-10 text-center">No.</th>
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
                    <th scope="col" class="px-4 py-3 w-48">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'contact') {{ $sortDirection }} @endif" wire:click="sortBy('contact')">
                            Contact
                            @if ($sortField == 'contact' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'contact' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-32">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'phone') {{ $sortDirection }} @endif" wire:click="sortBy('phone')">
                            Phone
                            @if ($sortField == 'phone' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'phone' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3 w-48">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'email') {{ $sortDirection }} @endif" wire:click="sortBy('email')">
                            Email
                            @if ($sortField == 'email' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'email' && $sortDirection == 'desc')
                                <flux:icon.chevron-down class="ml-2 size-4" />
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-3">
                        <div class="flex items-center cursor-pointer @if ($sortField == 'address') {{ $sortDirection }} @endif" wire:click="sortBy('address')">
                            Address
                            @if ($sortField == 'address' && $sortDirection == 'asc')
                                <flux:icon.chevron-up class="ml-2 size-4" />
                            @elseif ($sortField == 'address' && $sortDirection == 'desc')
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
                @if(isset($vendors) && $vendors->count() > 0)
                    @foreach($vendors as $index => $vendor)
                        <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700/50" wire:loading.class="opacity-50">
                            <td class="px-4 py-2 text-center text-gray-900 dark:text-white">{{ $vendors->firstItem() + $index }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 dark:text-zinc-300">{{ $vendor->name }}</span>
                                    @if($vendor->created_at->diffInDays() <= 7)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            New
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">{{ $vendor->contact ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">{{ $vendor->phone ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-600 dark:text-zinc-300">
                                @if($vendor->email)
                                    <a href="mailto:{{ $vendor->email }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">{{ $vendor->email }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap lg:whitespace-normal text-gray-600 dark:text-zinc-300">
                                @if($vendor->address)
                                    <span class="block max-w-48 truncate" title="{{ $vendor->address }}">{{ $vendor->address }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-zinc-400">
                                {{ $vendor->created_at->format('M d, Y') }}
                                <div class="text-gray-400 dark:text-zinc-500">{{ $vendor->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @can('vendor.view')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('vendors.show', $vendor->id) }}" wire:navigate tooltip="Show">
                                        <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
                                    </flux:button>
                                @endcan

                                @can('vendor.edit')
                                    <flux:button variant="ghost" size="xs" square href="{{ route('vendors.edit', $vendor->id) }}" wire:navigate tooltip="Edit">
                                        <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
                                    </flux:button>
                                @endcan

                                @can('vendor.delete')
                                    <flux:modal.trigger name="delete-vendor">
                                        <flux:button variant="ghost" size="xs" square class="cursor-pointer" wire:click="setVendorToDelete({{ $vendor->id }})" tooltip="Delete">
                                            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
                                        </flux:button>
                                    </flux:modal.trigger>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 border-b dark:border-zinc-700 border-gray-200">
                        <td class="px-4 py-2 text-gray-600 dark:text-zinc-300 text-center" colspan="8">
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
        {{ $vendors->links(data: ['scrollTo' => false]) }}
    </div>

    <flux:modal name="delete-vendor" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete vendor?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this vendor.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" class="cursor-pointer">Delete Vendor</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
