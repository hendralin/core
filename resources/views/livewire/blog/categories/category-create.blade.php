<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Blog Category') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Add a new category for organizing your blog posts') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession

    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <form wire:submit="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category Name -->
            <div class="md:col-span-2">
                <flux:field>
                    <flux:label for="name">{{ __('Category Name') }} <span class="text-red-500">*</span></flux:label>
                    <flux:input
                        id="name"
                        wire:model="name"
                        type="text"
                        placeholder="Enter category name"
                    />
                    <flux:error name="name" />
                </flux:field>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <flux:field>
                    <flux:label for="description">{{ __('Description') }}</flux:label>
                    <flux:textarea
                        id="description"
                        wire:model="description"
                        placeholder="Enter category description (optional)"
                        rows="3"
                    />
                    <flux:error name="description" />
                    <flux:description>Provide a brief description of what this category is about.</flux:description>
                </flux:field>
            </div>

            <!-- Color Selection -->
            <div class="md:col-span-2">
                <flux:field>
                    <flux:label>{{ __('Category Color') }} <span class="text-red-500">*</span></flux:label>
                    <div class="space-y-6">
                        @php
                            $groupedColors = app(\App\Services\CategoryService::class)->getColorsGroupedByCategory();
                        @endphp

                        @foreach($groupedColors as $categoryName => $colors)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-zinc-100 mb-3">{{ $categoryName }} Colors</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                    @foreach($colors as $key => $color)
                                        <label class="relative">
                                            <input
                                                type="radio"
                                                wire:model.live="color"
                                                value="{{ $key }}"
                                                class="sr-only peer"
                                            />
                                            <div class="flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer transition-all
                                                {{ $color == $key ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 ring-2 ring-blue-500/20' : 'border-gray-200 dark:border-zinc-700 hover:border-gray-300 dark:hover:border-zinc-600' }}">
                                                <div class="w-8 h-8 rounded-full mb-2 shadow-sm" style="background-color: {{ $color['hex'] }}"></div>
                                                <span class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $color['name'] }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <!-- Custom Color Option -->
                        <div class="border-gray-200 dark:border-zinc-700">
                            <flux:field>
                                <flux:label>{{ __('Custom Hex Color') }}</flux:label>
                                <div class="w-16">
                                    <input
                                    type="color"
                                    wire:model.live="customColor"
                                    class="h-12 cursor-pointer"
                                    />
                                </div>
                            </flux:field>
                            <flux:description>Or choose a custom color using the color picker.</flux:description>
                        </div>
                    </div>
                    <flux:error name="color" />
                    <flux:description>Choose a color from the palette or use the custom color picker to visually distinguish this category.</flux:description>
                </flux:field>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-zinc-700">
            <flux:button variant="ghost" href="{{ route('blog.categories.index') }}" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Create Category') }}</span>
                <span wire:loading>{{ __('Creating...') }}</span>
            </flux:button>
        </div>
    </form>
</div>
