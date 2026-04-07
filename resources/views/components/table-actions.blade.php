{{-- resources/views/components/table-actions.blade.php --}}
@props(['user'])

<div class="flex justify-end space-x-2">
    @can('user.view')
        <flux:button variant="ghost" size="xs" square href="{{ route('users.show', $user->id) }}" wire:navigate tooltip="Show">
            <flux:icon.eye variant="mini" class="text-green-500 dark:text-green-300" />
        </flux:button>
    @endcan

    @can('user.edit')
        <flux:button variant="ghost" size="xs" square href="{{ route('users.edit', $user->id) }}" wire:navigate tooltip="Edit">
            <flux:icon.pencil-square variant="mini" class="text-indigo-500 dark:text-indigo-300" />
        </flux:button>
    @endcan

    @if (auth()->user()->hasRole('superadmin') && $user->id !== auth()->id())
        <form method="POST" action="{{ route('users.impersonate', $user) }}" class="inline">
            @csrf
            <flux:button
                type="submit"
                variant="ghost"
                size="xs"
                square
                tooltip="{{ __('Impersonate') }}"
                class="cursor-pointer"
            >
                <flux:icon.user-circle variant="mini" class="text-amber-500 dark:text-amber-300" />
            </flux:button>
        </form>
    @endif

    @can('user.delete')
        <flux:button variant="ghost" size="xs" square href="#" wire:click="delete({{ $user->id }})" wire:confirm="Are you sure to remove this user?" tooltip="Delete">
            <flux:icon.trash variant="mini" class="text-red-500 dark:text-red-300" />
        </flux:button>
    @endcan
</div>
