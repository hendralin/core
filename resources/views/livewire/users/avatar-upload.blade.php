<div class="space-y-6">
    <!-- Current Avatar Display -->
    <div class="flex flex-col items-center space-y-4">
        <div class="relative">
            <img src="{{ $user->avatar_url }}"
                 alt="{{ $user->name }}"
                 class="h-32 w-32 rounded-full object-cover border-4 border-gray-200 dark:border-zinc-700">

            @if($user->avatar)
                <button wire:click="removeAvatar"
                        wire:confirm="Apakah Anda yakin ingin menghapus avatar ini?"
                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg transition-colors"
                        title="Remove Avatar">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>

        <div class="text-center">
            <flux:heading size="md">{{ $user->name }}</flux:heading>
            <flux:text class="text-gray-600 dark:text-zinc-400">{{ $user->email }}</flux:text>
        </div>
    </div>

    <!-- Avatar Upload Form -->
    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
        <flux:heading size="md" class="mb-4">Update Avatar</flux:heading>

        <div class="space-y-4">
            <!-- File Input -->
            <div>
                <label for="avatar-upload" class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                    Choose New Avatar
                </label>
                <input type="file"
                       id="avatar-upload"
                       wire:model="avatar"
                       accept="image/*"
                       class="block w-full text-sm text-gray-500 dark:text-zinc-400
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-medium
                              file:bg-blue-50 file:text-blue-700
                              dark:file:bg-blue-900 dark:file:text-blue-300
                              hover:file:bg-blue-100 dark:hover:file:bg-blue-800
                              file:cursor-pointer file:transition-colors">

                @error('avatar')
                    <flux:text class="text-red-500 text-sm mt-1">{{ $message }}</flux:text>
                @enderror
            </div>

            <!-- Preview -->
            @if($avatar)
                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <img src="{{ $avatar->temporaryUrl() }}"
                         alt="Preview"
                         class="h-20 w-20 rounded-full object-cover border-2 border-gray-300 dark:border-zinc-600">
                    <div class="flex-1">
                        <flux:text class="font-medium">{{ $avatar->getClientOriginalName() }}</flux:text>
                        <flux:text class="text-sm text-gray-600 dark:text-zinc-400">
                            {{ number_format($avatar->getSize() / 1024, 1) }} KB
                        </flux:text>
                    </div>
                    <button wire:click="$set('avatar', null)"
                            class="text-red-500 hover:text-red-700 transition-colors"
                            title="Remove">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Upload Button -->
            <div class="flex items-center space-x-3">
                <flux:button wire:click="uploadAvatar"
                           :disabled="!$avatar || $isUploading"
                           variant="primary"
                           size="sm"
                           class="cursor-pointer"
                           :loading="$isUploading">
                    @if($isUploading)
                        Uploading...
                    @else
                        Upload Avatar
                    @endif
                </flux:button>

                @if(!$avatar)
                    <flux:text class="text-sm text-gray-500 dark:text-zinc-400">
                        Select an image file (max 2MB, JPEG/PNG/JPG/GIF/WebP)
                    </flux:text>
                @endif
            </div>
        </div>
    </div>

    <!-- Guidelines -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <svg class="h-5 w-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <flux:heading size="sm" class="text-blue-800 dark:text-blue-200 mb-1">Avatar Guidelines</flux:heading>
                <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                    <li>• Recommended size: 128x128 pixels or larger (square images work best)</li>
                    <li>• Maximum file size: 2MB</li>
                    <li>• Supported formats: JPEG, PNG, GIF, WebP</li>
                    <li>• Images will be automatically cropped to a circle</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @session('success')
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center space-x-3">
                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <flux:text class="text-green-800 dark:text-green-200">{{ $value }}</flux:text>
            </div>
        </div>
    @endsession

    @session('error')
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center space-x-3">
                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <flux:text class="text-red-800 dark:text-red-200">{{ $value }}</flux:text>
            </div>
        </div>
    @endsession
</div>
