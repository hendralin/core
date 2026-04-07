<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        @if (session('is_impersonating'))
            <flux:callout icon="exclamation-triangle" variant="warning" class="mb-4" inline>
                <flux:callout.heading>{{ __('Impersonating user') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('You are viewing the app as :name.', ['name' => auth()->user()->name]) }}
                </flux:callout.text>
                <form method="POST" action="{{ route('impersonate.leave') }}" class="mt-3">
                    @csrf
                    <flux:button type="submit" variant="primary" size="sm" class="cursor-pointer">
                        {{ __('Stop impersonating') }}
                    </flux:button>
                </form>
            </flux:callout>
        @endif

        {{ $slot }}
    </flux:main>

    {{-- @include('partials.footer') --}}
</x-layouts::app.sidebar>
