@include('partials.trix-cdn')

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create New Post') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Write and publish your blog post') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @session('success')
        <x-alert type="success" class="mb-4">{{ $value }}</x-alert>
    @endsession
    @session('error')
        <x-alert type="error" class="mb-4">{{ $value }}</x-alert>
    @endsession

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 gap-6">
            <flux:field>
                <flux:label for="title">{{ __('Title') }} <span class="text-red-500">*</span></flux:label>
                <flux:input
                    id="title"
                    wire:model.live.debounce="title"
                    type="text"
                    placeholder="{{ __('Enter post title') }}"
                />
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label for="excerpt">{{ __('Excerpt') }}</flux:label>
                <flux:textarea
                    id="excerpt"
                    wire:model="excerpt"
                    placeholder="{{ __('A short summary of your post (optional)') }}"
                    rows="2"
                />
                <flux:error name="excerpt" />
                <flux:description>{{ __('This will appear in post previews and search results') }}</flux:description>
            </flux:field>

            {{-- Content: Trix editor --}}
            <flux:field>
                <flux:label for="x-content">{{ __('Content') }} <span class="text-red-500">*</span></flux:label>
                <div wire:ignore>
                    <input type="hidden" name="content" id="x-content">
                    <trix-editor
                        input="x-content"
                        class="trix-content border border-zinc-300 dark:border-zinc-600 rounded-md min-h-50 bg-white dark:bg-zinc-900"
                        x-data
                        x-on:trix-change="$wire.content = $event.target.value"
                    ></trix-editor>
                </div>
                <flux:error name="content" />
            </flux:field>

            {{-- Featured Image --}}
            <flux:field>
                <flux:label>{{ __('Featured Image') }}</flux:label>
                <input
                    type="file"
                    wire:model="featured_image"
                    accept="image/*"
                    class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700 dark:file:bg-zinc-700 dark:file:text-zinc-200 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-600"
                />
                <flux:error name="featured_image" />
                @if ($featured_image)
                    <div class="mt-3" wire:transition>
                        <img src="{{ $featured_image->temporaryUrl() }}" class="h-32 w-auto rounded border border-zinc-300 dark:border-zinc-600" alt="{{ __('Preview') }}">
                    </div>
                @endif
                <div wire:loading wire:target="featured_image" class="mt-2 text-sm text-zinc-500">{{ __('Uploading...') }}</div>
            </flux:field>

            {{-- Categories --}}
            <flux:field>
                <flux:label>{{ __('Categories') }} <span class="text-red-500">*</span></flux:label>
                <div class="space-y-2 max-h-48 overflow-y-auto rounded-md border border-zinc-300 dark:border-zinc-600 p-3 bg-white dark:bg-zinc-900">
                    <flux:checkbox.group wire:model="selectedCategories">
                        @foreach ($categories as $category)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <flux:checkbox value="{{ $category->id }}" />
                                <span class="inline-block w-3 h-3 rounded-full shrink-0" style="background-color: {{ $category->color }}"></span>
                                <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </flux:checkbox.group>
                </div>
                <flux:error name="selectedCategories" />
            </flux:field>

            {{-- Tags --}}
            <flux:field>
                <flux:label>{{ __('Tags') }}</flux:label>
                <div class="space-y-2 max-h-48 overflow-y-auto rounded-md border border-zinc-300 dark:border-zinc-600 p-3 bg-white dark:bg-zinc-900">
                    <flux:checkbox.group wire:model="selectedTags">
                        @foreach ($tags as $tag)
                            <flux:checkbox label="{{ $tag->name }}" value="{{ $tag->id }}" />
                        @endforeach
                    </flux:checkbox.group>
                </div>
                <flux:error name="selectedTags" />
                <flux:description>{{ __('Select relevant tags to help readers find your content') }}</flux:description>
            </flux:field>

            {{-- Status --}}
            <flux:field>
                <flux:label>{{ __('Status') }}</flux:label>
                <flux:radio.group wire:model="status" class="space-y-3">
                    <flux:radio
                        value="draft"
                        :label="__('Draft')"
                        :description="__('Save as draft, not visible to readers')"
                    />
                    @can('blog.post.publish')
                        <flux:radio
                            value="published"
                            :label="__('Published')"
                            :description="__('Publish immediately, visible to all readers')"
                        />
                    @endcan
                </flux:radio.group>
                <flux:error name="status" />
            </flux:field>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button variant="ghost" href="{{ route('blog.posts.index') }}" wire:navigate>{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Create Post') }}</span>
                <span wire:loading>{{ __('Creating...') }}</span>
            </flux:button>
        </div>
    </form>
</div>
