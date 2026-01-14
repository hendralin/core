@if (auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
    <x-layouts.app-admin :title="__('Dashboard')">
        <livewire:dashboard.dashboard-index />
    </x-layouts.app-admin>
@else
    <x-layouts.app-admin :title="__('Dashboard')">
        <livewire:dashboard.dashboard-index />
    </x-layouts.app-admin>
@endif
