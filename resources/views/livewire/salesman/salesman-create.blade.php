<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Salesman') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for create new salesman') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('salesmen.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Kembali ke Salesman">Back</flux:button>

        <div class="w-full max-w-lg">
            <form wire:submit="submit" class="mt-6 space-y-6" enctype="multipart/form-data">
                <flux:input wire:model="name" label="Name" placeholder="Name..." />
                <div class="grid grid-cols-2 gap-6">
                    <flux:input wire:model="phone" label="Phone" placeholder="Phone number..." />
                    <flux:input wire:model="email" label="Email" placeholder="Email address..." />
                </div>
                <flux:textarea wire:model="address" label="Address" placeholder="Address..." />

                <div class="space-y-3">
                    <flux:heading size="sm">Tanda tangan (wajib)</flux:heading>
                    <div class="flex items-start gap-4">
                        <div class="shrink-0">
                            @if($signature)
                                <div class="relative">
                                    <img src="{{ $signature->temporaryUrl() }}" alt="Preview tanda tangan" class="max-h-28 max-w-40 object-contain rounded-lg border-2 border-zinc-200 dark:border-zinc-600">
                                    <button type="button" wire:click="removeSignature" class="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600" title="Hapus file">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @else
                                <div class="flex h-24 w-32 items-center justify-center rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600">
                                    <flux:icon.photo class="h-8 w-8 text-zinc-400" />
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <flux:input
                                type="file"
                                wire:model="signature"
                                label="Upload gambar tanda tangan"
                                accept="image/jpeg,image/jpg,image/png,image/webp"
                            />
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Format: JPEG, PNG, atau WebP. Maks. 2MB.</p>
                            @error('signature')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <flux:button type="submit" variant="primary" class="cursor-pointer">Submit</flux:button>
            </form>
        </div>
    </div>
</div>
