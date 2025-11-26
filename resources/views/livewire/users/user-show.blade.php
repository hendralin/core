<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show User') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('This page is for show user') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('users.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Users">Back</flux:button>
        @can('user.edit')
            <flux:button variant="filled" size="sm" href="{{ route('users.edit', $user->id) }}" wire:navigate icon="pencil-square" class="ml-1">Edit</flux:button>
        @endcan

        <div class="mt-4 w-full max-w-lg">
            <!-- User Avatar and Basic Info -->
            <div class="flex items-center mb-6">
                <img src="{{ $user->avatar_url }}" class="h-20 w-20 rounded-full" alt="{{ $user->name }}">
                <div class="ml-6">
                    <flux:heading size="xl">{{ $user->name }}</flux:heading>
                    <flux:text>{{ $user->email }}</flux:text>
                    <div class="mt-2">
                        @if ($user->status == 0)
                            <flux:badge color="red">Inactive</flux:badge>
                        @elseif ($user->status == 1)
                            <flux:badge color="green">Active</flux:badge>
                        @else
                            <flux:badge color="yellow">Pending</flux:badge>
                        @endif
                        @if($user->is_email_verified)
                            <flux:badge color="green" class="ml-2">Verified</flux:badge>
                        @else
                            <flux:badge color="yellow" class="ml-2">Unverified</flux:badge>
                        @endif
                    </div>
                </div>
            </div>

            <flux:heading size="lg">Account Information</flux:heading>
            <div class="grid grid-cols-2 gap-4 mt-3 mb-6">
                <div>
                    <flux:heading size="sm">Joined</flux:heading>
                    <flux:text class="mt-1">{{ $user->created_at->format('M d, Y') }}</flux:text>
                </div>
                <div>
                    <flux:heading size="sm">Account Age</flux:heading>
                    <flux:text class="mt-1">{{ $user->account_age }}</flux:text>
                </div>
                @if($user->last_login_at)
                <div>
                    <flux:heading size="sm">Last Login</flux:heading>
                    <flux:text class="mt-1">{{ $user->last_login_at->format('M d, Y H:i') }}</flux:text>
                </div>
                @endif
                <div>
                    <flux:heading size="sm">Timezone</flux:heading>
                    <flux:text class="mt-1">{{ $user->timezone }}</flux:text>
                </div>
            </div>

            @if($user->phone || $user->birth_date || $user->address)
            <flux:heading size="lg" class="mt-6">Personal Information</flux:heading>
            <div class="mt-3 space-y-3">
                @if($user->phone)
                <div>
                    <flux:heading size="sm">Phone</flux:heading>
                    <flux:text class="mt-1">{{ $user->phone }}</flux:text>
                </div>
                @endif
                @if($user->birth_date)
                <div>
                    <flux:heading size="sm">Birth Date</flux:heading>
                    <flux:text class="mt-1">{{ $user->birth_date->format('M d, Y') }}</flux:text>
                </div>
                @endif
                @if($user->address)
                <div>
                    <flux:heading size="sm">Address</flux:heading>
                    <flux:text class="mt-1">{{ $user->address }}</flux:text>
                </div>
                @endif
            </div>
            @endif

            <flux:heading size="lg" class="mt-6">Roles & Permissions</flux:heading>
            <flux:text class="mt-3">
                @if ($user->roles)
                    @foreach ($user->roles as $role)
                        <flux:badge color="blue" class="mr-2 mb-2">{{ $role->name }}</flux:badge>
                    @endforeach
                @else
                    <flux:text class="text-gray-500">No roles assigned</flux:text>
                @endif
            </flux:text>
        </div>
    </div>
</div>
