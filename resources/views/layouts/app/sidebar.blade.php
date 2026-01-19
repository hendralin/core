<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')

        @stack('css')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
        <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            {{-- collapsible sidebar --}}
            <flux:sidebar.header>
                @if (auth()->user()->company_logo)
                <flux:sidebar.brand
                    href="#"
                    logo="{{ auth()->user()->company_logo_url }}"
                    logo:dark="{{ auth()->user()->company_logo_url }}"
                    name="{{ env('APP_NAME', 'Laravel Starter Kit') }}"
                />
                @else
                <flux:sidebar.brand
                    href="#"
                    logo="{{ asset('photos/logo/favicon-32x32.png') }}"
                    logo:dark="{{ asset('photos/logo/favicon-32x32.png') }}"
                    name="{{ env('APP_NAME', 'Laravel Starter Kit') }}"
                />
                @endif
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:hidden max-lg:hidden">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:sidebar.item>

                @if(auth()->user()->can('company.view'))
                    <flux:sidebar.group expandable icon="cog-6-tooth" heading="Setup" class="grid">
                        @if (auth()->user()->can('company.view') ||
                        auth()->user()->can('company.edit'))
                            <flux:sidebar.item icon="building-office" :href="route('company.show')" :current="request()->routeIs('company.show')" wire:navigate>{{ __('Company') }}</flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif

                @if (auth()->user()->can('user.view') ||
                auth()->user()->can('role.view'))
                    <flux:sidebar.group expandable icon="key" heading="Access Control" class="grid">
                        @if (auth()->user()->can('user.view') ||
                        auth()->user()->can('user.create') ||
                        auth()->user()->can('user.edit') ||
                        auth()->user()->can('user.delete'))
                            <flux:sidebar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>{{ __('Users') }}</flux:sidebar.item>
                        @endif

                        @if (auth()->user()->can('role.view') ||
                        auth()->user()->can('role.create') ||
                        auth()->user()->can('role.edit') ||
                        auth()->user()->can('role.delete'))
                            <flux:sidebar.item icon="link-slash" :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate>{{ __('Roles') }}</flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif

                @if (auth()->user()->can('stock-summary.view'))
                    <flux:sidebar.group expandable icon="chart-bar" heading="Trading Summary" class="grid">
                        @if (auth()->user()->can('stock-summary.view'))
                            <flux:sidebar.item icon="chart-bar" :href="route('stock-summary.index')" :current="request()->routeIs('stock-summary.index')" wire:navigate>{{ __('Stock Summary') }}</flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif

                @if (auth()->user()->can('admin.signal.view'))
                    <flux:sidebar.group expandable icon="sparkles" heading="Featuring" class="grid">
                        @if (auth()->user()->can('admin.signal.view') ||
                        auth()->user()->can('admin.signal.create') ||
                        auth()->user()->can('admin.signal.edit') ||
                        auth()->user()->can('admin.signal.publish') ||
                        auth()->user()->can('admin.signal.unpublish'))
                            <flux:sidebar.item icon="signal" :href="route('admin.signals.index')" :current="request()->routeIs('admin.signals.*')" wire:navigate>{{ __('Signals') }}</flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif

                <flux:sidebar.group expandable icon="wrench-screwdriver" heading="Tool" class="grid">
                    @if (auth()->user()->can('backup-restore.view') || auth()->user()->can('backup-restore.create'))
                        <flux:sidebar.item icon="wrench" :href="route('backup-restore.index')" :current="request()->routeIs('backup-restore.index')" wire:navigate>{{ __('Backup and Restore') }}</flux:sidebar.item>
                    @endif

                    <flux:sidebar.item icon="information-circle" :href="route('about.index')" :current="request()->routeIs('about.index')" wire:navigate>{{ __('About') }}</flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:sidebar.spacer class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:hidden max-lg:hidden" />

            <flux:dropdown position="top" align="start" class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:hidden max-lg:hidden">
                <flux:sidebar.profile
                    class="cursor-pointer"
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    :avatar="auth()->user()->avatar_url"
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

            {{-- stashable sidebar --}}
            <flux:navlist variant="outline" class="in-data-flux-sidebar-collapsed-desktop:hidden">
                <flux:navlist.group :heading="__('Theming')" class="grid" @keydown.window="if ($event.shiftKey && ($event.key === 'd' || $event.key === 'D')) $flux.dark = !$flux.dark">
                    <flux:navlist.item x-show="$flux.dark" icon="moon" x-data>
                        <flux:tooltip content="Switch to light mode" position="right">
                            <flux:switch x-model="$flux.dark" label="Dark" class="cursor-pointer" />
                        </flux:tooltip>
                    </flux:navlist.item>
                    <flux:navlist.item x-show="!$flux.dark" icon="sun" x-data>
                        <flux:tooltip content="Switch to dark mode" position="right">
                            <flux:switch x-model="$flux.dark" label="Light" class="cursor-pointer" />
                        </flux:tooltip>
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
                        @endif

                        @if (auth()->user()->can('role.view') ||
                        auth()->user()->can('role.create') ||
                        auth()->user()->can('role.edit') ||
                        auth()->user()->can('role.delete'))
                            <flux:navlist.item icon="link-slash" :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate>{{ __('Roles') }}</flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif

                @if (auth()->user()->can('stock-summary.view'))
                    <flux:navlist.group :heading="__('Trading Summary')" class="grid">
                        @if (auth()->user()->can('stock-summary.view'))
                            <flux:navlist.item icon="chart-bar" :href="route('stock-summary.index')" :current="request()->routeIs('stock-summary.index')" wire:navigate>{{ __('Stock Summary') }}</flux:navlist.item>
                        @endif
                    </flux:navlist.group>
                @endif

                @if (auth()->user()->can('admin.signal.view'))
                    <flux:navlist.group :heading="__('Featuring')" class="grid">
                        @if (auth()->user()->can('admin.signal.view') ||
                        auth()->user()->can('admin.signal.create') ||
                        auth()->user()->can('admin.signal.edit') ||
                        auth()->user()->can('admin.signal.publish') ||
                        auth()->user()->can('admin.signal.unpublish'))
                            <flux:navlist.item icon="signal" :href="route('admin.signals.index')" :current="request()->routeIs('admin.signals.*')" wire:navigate>{{ __('Signals') }}</flux:navlist.item>
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

            <flux:spacer class="in-data-flux-sidebar-collapsed-desktop:hidden" />

            {{-- <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist> --}}

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:lg:block" position="bottom" align="start">
                <flux:profile
                    class="cursor-pointer in-data-flux-sidebar-collapsed-desktop:hidden"
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
