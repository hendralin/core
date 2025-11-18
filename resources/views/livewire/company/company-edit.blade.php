<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Company Info') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form for edit company info') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        <div class="flex items-center justify-between mb-4">
            <flux:button variant="ghost" size="sm" href="{{ route('company.show') }}" wire:navigate icon="arrow-uturn-left">Back to Company Info</flux:button>

            <!-- Auto-save Status and Draft Actions -->
            <div class="flex items-center space-x-4">
                @if($autoSaveStatus)
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        @if(str_contains($autoSaveStatus, 'saved'))
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @elseif(str_contains($autoSaveStatus, 'Unsaved'))
                            <svg class="w-4 h-4 mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        @endif
                        <span>{{ $autoSaveStatus }}</span>
                    </div>
                @endif

                @if(session()->has('company_draft'))
                    <flux:button wire:click="restoreDraft" variant="outline" size="sm" icon="arrow-path">
                        Restore Draft
                    </flux:button>
                @endif
            </div>
        </div>

        <!-- Success Message -->
        @session('success')
            <div class="flex items-center p-4 mb-6 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-300 dark:border-green-800" role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ $value }}</span>
            </div>
        @endsession

        <!-- Error Message -->
        @session('error')
            <div class="flex items-center p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-red-900 dark:text-red-300 dark:border-red-800" role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="font-medium">{{ $value }}</span>
            </div>
        @endsession

        <!-- General Error -->
        @error('general')
            <div class="flex items-center p-4 mb-6 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-red-900 dark:text-red-300 dark:border-red-800" role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="font-medium">{{ $message }}</span>
            </div>
        @enderror

        <form wire:submit="submit" class="mt-6 space-y-8">
            <!-- Logo Upload Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Company Logo
                </flux:heading>

                <div class="flex items-center space-x-6">
                    <!-- Logo Preview -->
                    <div class="flex-shrink-0">
                        @if($logo)
                            <!-- Preview for newly uploaded file -->
                            <div class="relative">
                                <img src="{{ $logo->temporaryUrl() }}" alt="New Logo Preview" class="w-24 h-24 object-contain rounded-lg border-2 border-blue-200 dark:border-blue-700">
                                <button type="button" wire:click="removeLogo" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors" title="Remove new logo">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @elseif($company->logo)
                            <!-- Preview for existing logo -->
                            <div class="relative">
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('logos')->url($company->logo) }}" alt="Current Logo" class="w-24 h-24 object-contain rounded-lg border-2 border-gray-200 dark:border-gray-700">
                                <button type="button" wire:click="removeLogo" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <!-- No logo placeholder -->
                            <div class="w-24 h-24 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Upload Input -->
                    <div class="flex-1">
                        <flux:input
                            type="file"
                            wire:model="logo"
                            label="Upload New Logo"
                            placeholder="Choose image file..."
                            accept="image/*"
                        />
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Supported formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB
                        </p>
                        @error('logo')
                            <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <flux:heading size="lg" class="mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Basic Information
                    </flux:heading>

                    <div class="space-y-4">
                        <flux:input wire:model="name" label="Company Name" placeholder="Enter company name..." />
                        @error('name') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                        <flux:input wire:model="tax_id" label="Tax ID / NPWP" placeholder="Enter tax ID..." />
                        @error('tax_id') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                        <flux:textarea wire:model="description" label="Description" placeholder="Enter company description..." rows="3" />
                        @error('description') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <flux:heading size="lg" class="mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact Information
                    </flux:heading>

                    <div class="space-y-4">
                        <flux:input wire:model="email" label="Email Address" placeholder="company@example.com" type="email" />
                        @error('email') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                        <flux:input wire:model="phone" label="Phone Number" placeholder="+1 (555) 123-4567" />
                        @error('phone') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                        <flux:input wire:model="website" label="Website" placeholder="https://www.company.com" />
                        @error('website') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Address - Full Width -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                    <flux:heading size="lg" class="mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Address
                    </flux:heading>

                    <flux:textarea wire:model="address" label="Full Address" placeholder="Enter complete company address..." rows="4" />
                    @error('address') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Social Media - Full Width -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                    <flux:heading size="lg" class="mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        Social Media Links
                    </flux:heading>

                    <!-- Existing Social Media Links -->
                    @if(!empty($socialMedia))
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Current Links:</h4>
                            <div class="space-y-2">
                                @foreach($socialMedia as $index => $social)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <span class="font-medium text-gray-900 dark:text-gray-100 capitalize">{{ $social['platform'] }}</span>
                                            <a href="{{ $social['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                                                {{ $social['url'] }}
                                            </a>
                                        </div>
                                        <button type="button" wire:click="removeSocialMedia({{ $index }})"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add New Social Media Form -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Add New Link:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <flux:input wire:model="newSocialPlatform" label="Platform" placeholder="e.g., Facebook, Twitter, Instagram" />
                            @error('newSocialPlatform') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                            <flux:input wire:model="newSocialUrl" label="URL" placeholder="https://..." />
                            @error('newSocialUrl') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                            <div class="flex items-end">
                                <flux:button wire:click="addSocialMedia" variant="outline" class="w-full">
                                    Add Link
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- License Information - Collapsible Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading size="lg" class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            License Information
                        </flux:heading>

                        <flux:button wire:click="toggleLicenseSection" variant="ghost" size="sm" icon="{{ $showLicenseSection ? 'chevron-up' : 'chevron-down' }}">
                            {{ $showLicenseSection ? 'Hide' : 'Show' }} License Settings
                        </flux:button>
                    </div>

                    @if($showLicenseSection)
                        <div class="space-y-6">
                            <!-- License Key and Type -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <flux:input wire:model="license_key" label="License Key" placeholder="LIC-XXXX-YYYY" />
                                    @error('license_key') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    <div class="mt-2">
                                        <flux:button wire:click="generateLicenseKey" variant="outline" size="sm" type="button">
                                            Generate Key
                                        </flux:button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">License Type</label>
                                    <select wire:model="license_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="trial">Trial</option>
                                        <option value="basic">Basic</option>
                                        <option value="premium">Premium</option>
                                        <option value="enterprise">Enterprise</option>
                                    </select>
                                    @error('license_type') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- License Status and Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">License Status</label>
                                    <select wire:model="license_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="active">Active</option>
                                        <option value="expired">Expired</option>
                                        <option value="suspended">Suspended</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    @error('license_status') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <flux:input wire:model="license_issued_at" label="Issued Date" type="date" />
                                    @error('license_issued_at') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <flux:input wire:model="license_expires_at" label="Expiration Date" type="date" />
                                    @error('license_expires_at') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Limits and Features -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <flux:input wire:model="max_users" label="Max Users" type="number" placeholder="Unlimited if empty" min="1" />
                                    @error('max_users') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <flux:input wire:model="max_storage_gb" label="Max Storage (GB)" type="number" placeholder="Unlimited if empty" min="1" />
                                    @error('max_storage_gb') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Features Management -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enabled Features</label>

                                <!-- Existing Features -->
                                @if(!empty($features_enabled))
                                    <div class="mb-4">
                                        <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Current Features:</h5>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($features_enabled as $index => $feature)
                                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    <span>{{ $feature }}</span>
                                                    <button type="button" wire:click="removeFeature({{ $index }})"
                                                            class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Add New Feature -->
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Add New Feature:</h5>
                                    <div class="flex gap-2">
                                        <flux:input wire:model="newFeature" placeholder="Enter feature name..." class="flex-1" />
                                        <flux:button wire:click="addFeature" variant="outline" type="button">
                                            Add Feature
                                        </flux:button>
                                    </div>
                                    @error('newFeature') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <flux:button type="submit" variant="primary" icon="check">
                    Save Changes
                </flux:button>
            </div>
        </form>
    </div>

    <!-- Auto-save JavaScript -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            let autoSaveTimeout;

            Livewire.on('auto-save-draft', () => {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    $wire.call('autoSaveDraft');
                }, 3000); // Auto-save after 3 seconds of inactivity
            });

            // Clear timeout when form is submitted
            document.addEventListener('submit', () => {
                clearTimeout(autoSaveTimeout);
            });
        });
    </script>
</div>
