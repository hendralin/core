@if (session('is_impersonating'))
    <flux:menu.separator />

    <flux:menu.radio.group>
        <form method="POST" action="{{ route('impersonate.leave') }}" class="w-full">
            @csrf
            <flux:menu.item as="button" type="submit" icon="arrow-uturn-left" class="w-full cursor-pointer text-amber-600 dark:text-amber-400">
                {{ __('Stop impersonating') }}
            </flux:menu.item>
        </form>
    </flux:menu.radio.group>
@endif
