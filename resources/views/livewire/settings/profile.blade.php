<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <!-- Avatar Upload Section -->
        <div class="my-6 space-y-6">
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- Current Avatar Display -->
                <div class="relative">
                    <img src="{{ auth()->user()->avatar_url }}"
                         alt="{{ auth()->user()->name }}"
                         class="h-24 w-24 rounded-full object-cover border-4 border-gray-200 dark:border-zinc-700">

                    @if(auth()->user()->avatar)
                        <button type="button"
                                wire:click="removeAvatar"
                                wire:confirm="Apakah Anda yakin ingin menghapus avatar ini?"
                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg transition-colors"
                                title="Remove Avatar">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                <!-- Avatar Upload Form -->
                <div class="flex-1 space-y-3">
                    <div>
                        <flux:label>{{ __('Profile Photo') }}</flux:label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="file"
                                   id="avatar-upload"
                                   wire:model="avatar"
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 dark:text-zinc-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-lg file:border-0
                                          file:text-sm file:font-medium
                                          file:bg-blue-50 file:text-blue-700
                                          dark:file:bg-blue-900 dark:file:text-blue-300
                                          hover:file:bg-blue-100 dark:hover:file:bg-blue-800
                                          file:cursor-pointer file:transition-colors">
                        </div>
                        @error('avatar')
                            <flux:text class="text-red-500 text-sm mt-1">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    <!-- Preview -->
                    @if($avatar)
                        <div class="flex items-center space-x-1 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <img src="{{ $avatar->temporaryUrl() }}"
                                 alt="Preview"
                                 class="h-12 w-12 rounded-full object-cover border-2 border-gray-300 dark:border-zinc-600">
                            <div class="flex-1 min-w-0">
                                <flux:text class="font-medium truncate">{{ $avatar->getClientOriginalName() }}</flux:text>
                                <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                                    {{ number_format($avatar->getSize() / 1024, 1) }} KB
                                </flux:text>
                            </div>
                            <button type="button"
                                    wire:click="$set('avatar', null)"
                                    class="text-red-500 hover:text-red-700 transition-colors shrink-0"
                                    title="Remove">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <!-- Upload Button -->
                    <div class="flex items-center gap-3">
                        <flux:button type="button"
                                   wire:click="uploadAvatar"
                                   :disabled="!$avatar || $isUploading"
                                   variant="filled"
                                   icon="arrow-up-tray"
                                   size="sm"
                                   class="cursor-pointer">
                            @if($isUploading)
                                Uploading...
                            @else
                                Upload Avatar
                            @endif
                        </flux:button>

                        @if(!$avatar)
                            <flux:text class="text-xs text-gray-500 dark:text-zinc-400">
                                Max 2MB (JPEG, PNG, GIF, WebP)
                            </flux:text>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Flash Messages for Avatar -->
            @if(session('avatar-success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                    <div class="flex items-center space-x-2">
                        <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <flux:text class="text-green-800 dark:text-green-200 text-sm">{{ session('avatar-success') }}</flux:text>
                    </div>
                </div>
            @endif

            @if(session('avatar-error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                    <div class="flex items-center space-x-2">
                        <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <flux:text class="text-red-800 dark:text-red-200 text-sm">{{ session('avatar-error') }}</flux:text>
                    </div>
                </div>
            @endif
        </div>

        <div class="border-t border-gray-200 dark:border-zinc-700 pt-6"></div>

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 font-medium dark:text-green-400! text-green-600!">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <!-- API Token Section -->
        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-zinc-700">
            <div class="space-y-4">
                <div>
                    <flux:heading size="md" class="mb-2">API Token</flux:heading>
                    <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                        Use this token to authenticate API requests. Keep it secure and do not share it publicly.
                    </flux:text>
                </div>

                @if(session('api-token-regenerated') && session('new-api-token'))
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-4">
                        <flux:text class="text-sm font-medium text-green-800 dark:text-green-200">
                            ⚠️ New API token generated! Please copy it now. You won't be able to see it again.
                        </flux:text>
                    </div>
                @endif

                <div class="space-y-3">
                    <div>
                        <flux:label>Your API Token</flux:label>
                        <div class="mt-1 flex items-center gap-2">
                            <div class="flex-1 relative">
                                <flux:input
                                    type="text"
                                    :value="session('new-api-token') ?? auth()->user()->api_token"
                                    readonly
                                    class="font-mono text-sm pr-20"
                                    id="api-token-input"
                                    icon="key"
                                />
                            </div>
                            <flux:button
                                type="button"
                                variant="ghost"
                                size="sm"
                                x-data="{ copied: false }"
                                x-on:click="
                                    navigator.clipboard.writeText('{{ session('new-api-token') ?? auth()->user()->api_token }}');
                                    copied = true;
                                    setTimeout(() => copied = false, 2000);
                                "
                                class="shrink-0"
                            >
                                <span x-show="!copied">Copy</span>
                                <span x-show="copied" class="text-green-600 dark:text-green-400">Copied!</span>
                            </flux:button>
                        </div>
                    </div>

                    <div>
                        <flux:button
                            type="button"
                            size="sm"
                            icon="arrow-path-rounded-square"
                            wire:click="regenerateApiToken"
                            wire:confirm="Are you sure? This will invalidate your current API token and you'll need to update all applications using it."
                            class="w-full sm:w-auto"
                        >
                            Regenerate Token
                        </flux:button>
                        <flux:text class="text-xs text-gray-500 dark:text-zinc-400 mt-2 block">
                            Regenerating will create a new token and invalidate the old one.
                        </flux:text>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mt-4">
                    <flux:heading size="sm" class="mb-2 text-blue-900 dark:text-blue-100">How to use API Token</flux:heading>
                    <flux:text class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <div class="mb-2 text-sm">Include the token in the Authorization header:</div>
                        <code class="block bg-blue-100 dark:bg-blue-900/40 p-2 rounded text-xs font-mono break-all">
                            Authorization: Bearer {{ session('new-api-token') ?? auth()->user()->api_token }}
                        </code>
                    </flux:text>
                </div>
            </div>
        </div>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
