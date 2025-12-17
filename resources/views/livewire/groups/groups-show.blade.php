<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Show Group') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('This page shows detailed group information') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <flux:button variant="primary" size="sm" href="{{ route('groups.index') }}" wire:navigate icon="arrow-uturn-left" tooltip="Back to Groups">Back</flux:button>

        <div class="mt-4 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Group Information -->
                <div class="lg:col-span-2">
                    <!-- Group Basic Info -->
                    <div class="mb-6 flex items-center gap-4">
                        @if($groupPictureUrl)
                            <div class="shrink-0">
                                <img src="{{ $groupPictureUrl }}"
                                     alt="Group Picture"
                                     class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 dark:border-zinc-700 cursor-pointer hover:border-blue-400 transition-colors"
                                     wire:click="previewGroupImage">
                            </div>
                        @else
                            <div class="shrink-0">
                                <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center">
                                    <flux:icon.user-group class="w-8 h-8 text-gray-400 dark:text-zinc-500" />
                                </div>
                            </div>
                        @endif
                        <div>
                            <flux:heading size="xl">{{ $group->name }}</flux:heading>
                            <div class="mt-2 flex items-center gap-2">
                                <flux:badge color="blue">{{ $group->group_wa_id }}</flux:badge>
                                @if($group->detail && isset($group->detail['isCommunity']) && $group->detail['isCommunity'])
                                    <flux:badge color="purple">Community</flux:badge>
                                @else
                                    <flux:badge color="green">Group</flux:badge>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Group Description -->
                    @if($group->detail && isset($group->detail['desc']))
                        <div class="mb-6">
                            <flux:heading size="lg">Description</flux:heading>
                            <flux:text class="mt-3">{{ $group->detail['desc'] }}</flux:text>
                        </div>
                    @endif

                    <flux:heading size="lg">Group Information</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 mb-6">
                        <div>
                            <flux:heading size="sm">WhatsApp ID</flux:heading>
                            <flux:text class="mt-1">{{ $group->group_wa_id }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Group Name</flux:heading>
                            <flux:text class="mt-1">{{ $group->name }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Session</flux:heading>
                            <flux:text class="mt-1">{{ $group->wahaSession->name ?? 'Unknown' }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Size</flux:heading>
                            <flux:text class="mt-1">{{ $group->detail['size'] ?? 'N/A' }} members</flux:text>
                        </div>
                        @if($group->detail && isset($group->detail['owner']))
                            <div>
                                <flux:heading size="sm">Owner</flux:heading>
                                <flux:text class="mt-1">{{ $group->detail['owner'] }}</flux:text>
                            </div>
                        @endif
                        @if($group->detail && isset($group->detail['ownerPn']))
                            <div>
                                <flux:heading size="sm">Owner Phone</flux:heading>
                                <flux:text class="mt-1">{{ $group->detail['ownerPn'] }}</flux:text>
                            </div>
                        @endif
                        @if($group->detail && isset($group->detail['creation']))
                            <div>
                                <flux:heading size="sm">Created</flux:heading>
                                <flux:text class="mt-1">{{ date('M d, Y H:i', $group->detail['creation']) }}</flux:text>
                            </div>
                        @endif
                        @if($group->detail && isset($group->detail['subjectTime']))
                            <div>
                                <flux:heading size="sm">Subject Updated</flux:heading>
                                <flux:text class="mt-1">{{ date('M d, Y H:i', $group->detail['subjectTime']) }}</flux:text>
                            </div>
                        @endif
                        @if($group->detail && isset($group->detail['restrict']))
                            <div>
                                <flux:heading size="sm">Admin Only Messages</flux:heading>
                                <flux:text class="mt-1">{{ $group->detail['restrict'] ? 'Yes' : 'No' }}</flux:text>
                            </div>
                        @endif
                        @if($group->detail && isset($group->detail['announce']))
                            <div>
                                <flux:heading size="sm">Admin Only Announcements</flux:heading>
                                <flux:text class="mt-1">{{ $group->detail['announce'] ? 'Yes' : 'No' }}</flux:text>
                            </div>
                        @endif
                        @if($group->detail && isset($group->detail['joinApprovalMode']))
                            <div>
                                <flux:heading size="sm">Join Approval Required</flux:heading>
                                <flux:text class="mt-1">{{ $group->detail['joinApprovalMode'] ? 'Yes' : 'No' }}</flux:text>
                            </div>
                        @endif
                        @if($group->detail && isset($group->detail['memberAddMode']))
                            <div>
                                <flux:heading size="sm">Admin Only Add Members</flux:heading>
                                <flux:text class="mt-1">{{ $group->detail['memberAddMode'] ? 'Yes' : 'No' }}</flux:text>
                            </div>
                        @endif
                    </div>

                    <flux:heading size="lg" class="mt-6">Timestamps</flux:heading>
                    <div class="grid grid-cols-2 gap-4 mt-3">
                        <div>
                            <flux:heading size="sm">Created</flux:heading>
                            <flux:text class="mt-1">{{ $group->created_at->format('M d, Y H:i') }}</flux:text>
                        </div>
                        <div>
                            <flux:heading size="sm">Updated</flux:heading>
                            <flux:text class="mt-1">{{ $group->updated_at->format('M d, Y H:i') }}</flux:text>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Participants -->
                <div>
                    @if($group->detail && isset($group->detail['participants']) && count($group->detail['participants']) > 0)
                        <flux:heading size="lg">Participants ({{ count($group->detail['participants']) }})</flux:heading>
                        <div class="mt-3 space-y-3 max-h-96 overflow-y-auto">
                            @foreach($group->detail['participants'] as $participant)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        @if(isset($participantPictures[$participant['id']]) && $participantPictures[$participant['id']])
                                            <img src="{{ $participantPictures[$participant['id']] }}"
                                                 alt="Profile"
                                                 class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-zinc-600 cursor-pointer hover:border-blue-400 transition-colors"
                                                 wire:init="loadParticipantPicture('{{ $participant['id'] }}')"
                                                 wire:click="previewImage('{{ $participantPictures[$participant['id']] }}', '{{ $participant['id'] }}')" />
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center"
                                                 wire:init="loadParticipantPicture('{{ $participant['id'] }}')">
                                                <flux:icon.user class="w-4 h-4 text-gray-400 dark:text-zinc-500" />
                                            </div>
                                        @endif
                                        <div>
                                            @if(isset($participantNames[$participant['id']]) && $participantNames[$participant['id']])
                                                <div class="text-sm font-medium">
                                                    {{ $participantNames[$participant['id']] }}
                                                </div>
                                            @else
                                                <div class="text-sm font-medium">{{ $participant['id'] }}</div>
                                            @endif
                                            @if(isset($participant['phoneNumber']))
                                                <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $participant['phoneNumber'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @if(isset($participant['admin']) && $participant['admin'])
                                        <flux:badge size="sm" color="{{ $participant['admin'] === 'superadmin' ? 'red' : 'orange' }}">
                                            {{ ucfirst($participant['admin']) }}
                                        </flux:badge>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <flux:modal name="image-preview-modal" class="max-w-2xl">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Profile Picture Preview</flux:heading>
            </div>

            @if($previewParticipantId)
                @php
                    $participant = collect($group->detail['participants'] ?? [])->firstWhere('id', $previewParticipantId);
                @endphp
                @if($participant)
                    <div class="bg-green-50 dark:bg-green-900/10 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <flux:heading size="sm" class="text-green-800 dark:text-green-200 mb-2">Participant Information</flux:heading>
                        <div class="space-y-1">
                            @if(isset($participantNames[$participant['id']]) && $participantNames[$participant['id']])
                                <div class="flex items-center gap-2">
                                    <flux:text class="text-sm"><strong>Name:</strong> {{ $participantNames[$participant['id']] }}</flux:text>
                                    @if(isset($participant['admin']) && $participant['admin'])
                                    <flux:badge size="sm" color="{{ $participant['admin'] === 'superadmin' ? 'red' : 'orange' }}" class="mt-1">
                                        {{ ucfirst($participant['admin']) }}
                                    </flux:badge>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <flux:text class="text-sm"><strong>WhatsApp ID:</strong> {{ $participant['id'] }}</flux:text>
                                    @if(isset($participant['admin']) && $participant['admin'])
                                    <flux:badge size="sm" color="{{ $participant['admin'] === 'superadmin' ? 'red' : 'orange' }}" class="mt-1">
                                        {{ ucfirst($participant['admin']) }}
                                    </flux:badge>
                                    @endif
                                </div>
                            @endif
                            @if(isset($participant['phoneNumber']))
                                <flux:text class="text-sm"><strong>Phone:</strong> {{ $participant['phoneNumber'] }}</flux:text>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            @if($previewImageUrl)
                <div class="flex justify-center">
                    <img src="{{ $previewImageUrl }}"
                         alt="Profile Picture Preview"
                         class="max-w-full max-h-96 rounded-lg object-contain border border-gray-200 dark:border-zinc-700" />
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost" class="cursor-pointer">Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>
