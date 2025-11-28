<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')

        @stack('css')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                @if (auth()->user()->company_logo)
                    <div class="flex aspect-square size-8 items-center justify-center rounded-md text-accent-foreground">
                        <img src="{{ auth()->user()->company_logo_url }}" class="size-6" alt="{{ env('APP_NAME', 'Laravel Starter Kit') }}">
                    </div>
                    <div class="ms-1 grid flex-1 text-start text-sm">
                        <span class="mb-0.5 truncate leading-tight font-semibold">{{ env('APP_NAME', 'Laravel Starter Kit') }}</span>
                    </div>
                @else
                    <x-app-logo />
                @endif
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Theming')" class="grid">
                    <flux:navlist.item x-show="$flux.dark" icon="moon" x-data>
                        <flux:switch x-model="$flux.dark" label="Dark" class="cursor-pointer" />
                    </flux:navlist.item>
                    <flux:navlist.item x-show="!$flux.dark" icon="sun" x-data>
                        <flux:switch x-model="$flux.dark" label="Light" class="cursor-pointer" />
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group :heading="__('Home')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>

                @if (auth()->user()->can('company.view'))
                    <flux:navlist.group :heading="__('Setup')" class="grid">
                        @if (auth()->user()->can('company.view') ||
                        auth()->user()->can('company.edit'))
                            <flux:navlist.item icon="building-office" :href="route('company.show')" :current="request()->routeIs('company.show')" wire:navigate>{{ __('Company') }}</flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif

                @if (auth()->user()->can('user.view') ||
                auth()->user()->can('role.view'))
                    <flux:navlist.group :heading="__('Access Control')" class="grid">
                        @if (auth()->user()->can('user.view') ||
                        auth()->user()->can('user.create') ||
                        auth()->user()->can('user.edit') ||
                        auth()->user()->can('user.delete'))
                            <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                        @endcan

                        @if (auth()->user()->can('role.view') ||
                        auth()->user()->can('role.create') ||
                        auth()->user()->can('role.edit') ||
                        auth()->user()->can('role.delete'))
                            <flux:navlist.item icon="link-slash" :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate>{{ __('Roles') }}</flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif

                <flux:navlist.group :heading="__('Tool')" class="grid">
                    @if (auth()->user()->can('backup-restore.view') || auth()->user()->can('backup-restore.create'))
                        <flux:navlist.item icon="wrench" :href="route('backup-restore.index')" :current="request()->routeIs('backup-restore.index')" wire:navigate>{{ __('Backup & Restore') }}</flux:navlist.item>
                    @endif
                    <flux:navlist.item icon="information-circle" :href="route('about.index')" :current="request()->routeIs('about.index')" wire:navigate>{{ __('About') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            {{-- <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist> --}}

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    class="cursor-pointer"
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    :avatar="auth()->user()->avatar_url"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex w-8 h-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        @if (auth()->user()->avatar)
                                            <flux:avatar src="{{ auth()->user()->avatar_url }}" />
                                        @else
                                            {{ auth()->user()->initials() }}
                                        @endif
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    :avatar="auth()->user()->avatar_url"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        @if (auth()->user()->avatar)
                                            <flux:avatar src="{{ auth()->user()->avatar_url }}" />
                                        @else
                                            {{ auth()->user()->initials() }}
                                        @endif
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts

        @stack('scripts')
    </body>
</html>
