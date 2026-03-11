<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>

    {{-- @include('partials.footer') --}}
</x-layouts::app.sidebar>
