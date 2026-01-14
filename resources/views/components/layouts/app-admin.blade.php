<x-layouts.app.sidebar :title="$title ?? null">
    <div class="[[data-flux-container]_&amp;]:px-0 [grid-area:main]" data-flux-main="">
        {{ $slot }}
    </div>
</x-layouts.app.sidebar>
