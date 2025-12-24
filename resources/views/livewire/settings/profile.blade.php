<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
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
