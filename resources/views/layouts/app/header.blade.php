<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        @stack('css')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
        <flux:header class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <flux:sidebar.brand
                    href="#"
                    logo="{{ asset('photos/logo/favicon-32x32.png') }}"
                    name="{{ env('APP_NAME', 'Laravel Starter Kit') }}"
                />
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>

                @if (auth()->user()->can('stock-summary.view'))
                    <flux:navbar.item icon="chart-bar" :href="route('stock-summary.index')" :current="request()->routeIs('stock-summary.index')" wire:navigate>
                        {{ __('Stock Summary') }}
                    </flux:navbar.item>
                @endif
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!" @keydown.window="if ($event.shiftKey && ($event.key === 'd' || $event.key === 'D')) $flux.dark = !$flux.dark">
                <flux:tooltip content="Switch to light mode" position="bottom">
                    <button
                        x-show="$flux.dark"
                        class="flex items-center justify-center h-10 w-10 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        x-data
                        x-model="$flux.dark"
                        @click="$flux.dark = !$flux.dark"
                    >
                        <flux:icon name="moon" variant="solid" class="size-5" />
                    </button>
                </flux:tooltip>
                <flux:tooltip content="Switch to dark mode" position="bottom">
                    <button
                        x-show="!$flux.dark"
                        class="flex items-center justify-center h-10 w-10 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        x-data
                        x-model="$flux.dark"
                        @click="$flux.dark = !$flux.dark"
                    >
                        <flux:icon name="sun" variant="solid" class="size-5" />
                    </button>
                </flux:tooltip>
            </flux:navbar>

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    :avatar="auth()->user()->avatar_url"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex w-8 h-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
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
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Home')">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                      {{ __('Dashboard') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
        @stack('scripts')
    </body>
</html>
