<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Contact') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('This page is for show contact details') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('contacts.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Contacts">Back</flux:button>

        <div class="mt-4 w-full max-w-4xl">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Contact Information -->
                <div class="lg:col-span-2">
                    <!-- Contact Basic Info -->
                    <div class="mb-6 flex items-center gap-4">
                        @if($profilePictureUrl)
                            <div class="shrink-0">
                                <button wire:click="$set('showProfileModal', true)" class="cursor-pointer hover:opacity-80 transition-opacity">
                                    <img src="{{ $profilePictureUrl }}" alt="Profile Picture" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 dark:border-zinc-700">
                                </button>
                            </div>
                        @else
                            <div class="shrink-0">
                                <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center">
                                    <flux:icon.user class="w-8 h-8 text-gray-400 dark:text-zinc-500" />
                                </div>
                            </div>
                        @endif
                        <div>
                            <flux:heading size="xl">{{ $contact->name }}</flux:heading>
                            <div class="mt-2">
                                <flux:badge color="blue">{{ $contact->wa_id }}</flux:badge>
                            </div>
                        </div>
                    </div>

                    <flux:heading size="lg">Contact Information</flux:heading>
                    <div class="grid grid-cols-1 gap-4 mt-3 mb-6">
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400">WhatsApp ID</flux:heading>
                            <flux:text class="mt-1">{{ $contact->wa_id }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400">Name</flux:heading>
                            <flux:text class="mt-1">{{ $contact->name }}</flux:text>
                        </div>
                        @if($contact->verified_name)
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400">Verified Name</flux:heading>
                            <flux:text class="mt-1">{{ $contact->verified_name }}</flux:text>
                        </div>
                        @endif
                        @if($contact->push_name)
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400">Push Name</flux:heading>
                            <flux:text class="mt-1">{{ $contact->push_name }}</flux:text>
                        </div>
                        @endif
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400">Session</flux:heading>
                            <flux:text class="mt-1">{{ $contact->wahaSession->name ?? 'Unknown' }}</flux:text>
                        </div>
                    </div>

                    <flux:heading size="lg" class="mt-6">Timestamps</flux:heading>
                    <div class="grid grid-cols-2 gap-4 mt-3">
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400">Created</flux:heading>
                            <flux:text class="mt-1">{{ $contact->created_at->format('M d, Y H:i') }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400">Updated</flux:heading>
                            <flux:text class="mt-1">{{ $contact->updated_at->format('M d, Y H:i') }}</flux:text>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Picture Preview Modal -->
    <flux:modal name="profile-modal" wire:model="showProfileModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Profile Picture') }}</flux:heading>
                <flux:subheading>{{ $contact->name }}</flux:subheading>
            </div>

            <div class="flex justify-center">
                <img src="{{ $profilePictureUrl }}" alt="Profile Picture" class="max-w-full max-h-96 rounded-lg object-contain">
            </div>

            <div class="flex justify-end space-x-2">
                <flux:spacer />
                <flux:button wire:click="$set('showProfileModal', false)" variant="ghost">{{ __('Close') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
