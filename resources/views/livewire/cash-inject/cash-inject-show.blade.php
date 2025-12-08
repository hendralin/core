@php
    use Illuminate\Support\Str;
@endphp

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Detail Inject Kas') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Detail informasi inject kas perusahaan') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('cash-injects.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Inject Kas">Back</flux:button>
        @can('cash-inject.edit')
            <flux:button variant="filled" size="sm" href="{{ route('cash-injects.edit', $cost) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
        @endcan

        <div class="mt-3">
            @session('success')
                <flux:callout color="green" icon="check-circle">
                    {{ session('success') }}
                </flux:callout>
            @endsession

            @session('error')
                <flux:callout color="red" icon="x-circle">
                    {{ session('error') }}
                </flux:callout>
            @endsession
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="lg" class="mb-4">Informasi Inject Kas</flux:heading>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <flux:heading size="md">Tanggal</flux:heading>
                                <flux:text class="mt-1">{{ Carbon\Carbon::parse($cost->cost_date)->format('d-m-Y') }}</flux:text>
                            </div>
                            <div>
                                <flux:heading size="sm">Total Inject</flux:heading>
                                <flux:text class="mt-1 font-medium text-lg">Rp {{ number_format($cost->total_price) }}</flux:text>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <flux:heading size="sm">Deskripsi</flux:heading>
                                <flux:text class="mt-1">{{ $cost->description }}</flux:text>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Section -->
                @if($cost->document)
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                        <flux:heading size="lg" class="mb-4">Dokumen Pendukung</flux:heading>

                        <div class="flex items-center gap-4">
                            @php
                                $documentPath = storage_path('app/public/photos/costs/' . $cost->document);
                                $isImage = file_exists($documentPath) && str_starts_with(mime_content_type($documentPath), 'image/');
                            @endphp

                            @if($isImage)
                                <img src="{{ asset('photos/costs/' . $cost->document) }}" alt="Document" class="w-32 h-32 object-contain rounded-lg border-2 border-gray-200 dark:border-zinc-700">
                            @else
                                <div class="w-32 h-32 border-2 border-gray-200 dark:border-zinc-700 rounded-lg flex items-center justify-center bg-gray-50 dark:bg-zinc-800">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-1">
                                <flux:heading size="sm">File: {{ $cost->document }}</flux:heading>
                                <flux:text class="mt-1 text-sm text-gray-600 dark:text-zinc-400">
                                    Uploaded on {{ Carbon\Carbon::parse($cost->created_at)->format('d-m-Y H:i') }}
                                </flux:text>
                                <div class="mt-2">
                                    <a href="{{ asset('photos/costs/' . $cost->document) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-md hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Document
                                    </a>
                                    <a href="{{ asset('photos/costs/' . $cost->document) }}" download class="inline-flex items-center gap-2 px-3 py-1 text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-md hover:bg-green-200 dark:hover:bg-green-800 transition-colors ml-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Metadata Card -->
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
                    <flux:heading size="md" class="mb-4">Informasi Tambahan</flux:heading>

                    <div class="space-y-3 text-sm">
                        <div>
                            <flux:text class="text-gray-600 dark:text-zinc-400">Dibuat Oleh</flux:text>
                            <flux:text class="block font-medium">{{ $cost->createdBy->name ?? 'Unknown' }}</flux:text>
                        </div>

                        <div>
                            <flux:text class="text-gray-600 dark:text-zinc-400">Dibuat Pada</flux:text>
                            <flux:text class="block font-medium">{{ Carbon\Carbon::parse($cost->created_at)->format('d-m-Y H:i') }}</flux:text>
                        </div>

                        @if($cost->updated_at !== $cost->created_at)
                            <div>
                                <flux:text class="text-gray-600 dark:text-zinc-400">Diubah Pada</flux:text>
                                <flux:text class="block font-medium">{{ Carbon\Carbon::parse($cost->updated_at)->format('d-m-Y H:i') }}</flux:text>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
