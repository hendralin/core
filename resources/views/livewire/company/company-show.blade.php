<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Company Info') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Company profile and information overview') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div>
        @session('success')
            <div class="flex items-center p-4 mb-6 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-green-900 dark:text-green-300 dark:border-green-800" role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-2 text-green-700 dark:text-green-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                </svg>
                <span class="font-medium">{{ $value }}</span>
            </div>
        @endsession

        @can('company.edit')
            <flux:button variant="primary" size="sm" href="{{ route('company.edit') }}" wire:navigate icon="pencil-square">Edit Company Info</flux:button>
        @endcan

        <!-- Company Logo Section -->
        @if($company->logo)
            <div class="mt-6 mb-8 flex justify-center">
                <div class="relative">
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('logos')->url($company->logo) }}" alt="Company Logo" class="w-32 h-32 object-contain rounded-lg border-2 border-gray-200 dark:border-gray-700">
                </div>
            </div>
        @endif

        <!-- Profile Completeness Indicator -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mt-6">
            <flux:heading size="lg" class="mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                Profile Completeness
            </flux:heading>

            <div class="space-y-4">
                <!-- Progress Bar -->
                <div class="w-full">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Profile Completion</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $completenessPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300 ease-in-out
                            @if($completenessPercentage >= 80) bg-green-500
                            @elseif($completenessPercentage >= 60) bg-blue-500
                            @elseif($completenessPercentage >= 40) bg-yellow-500
                            @else bg-red-500
                            @endif"
                            style="width: {{ $completenessPercentage }}%"></div>
                    </div>
                </div>

                <!-- Completion Tips -->
                @if(count($completenessTips) > 0)
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">💡 Tips to improve your profile:</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            @foreach($completenessTips as $tip)
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    {{ $tip }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <div class="flex items-center text-green-600 dark:text-green-400">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm font-medium">Your company profile is complete!</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mt-6">
            <flux:heading size="lg" class="mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Recent Activity
            </flux:heading>

            <div class="space-y-3">
                @forelse($this->getActivities() as $activity)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $activity->description }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                by {{ $activity->causer ? $activity->causer->name : 'System' }}
                                {{ $activity->created_at->diffForHumans() }}
                            </p>
                            @if($activity->properties && count($activity->properties->get('attributes', [])) > 0)
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Changed:</span>
                                    {{ implode(', ', array_keys($activity->properties->get('attributes', []))) }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No activity recorded yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Basic Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Basic Information
                </flux:heading>

                <div class="space-y-4">
                    <div>
                        <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-1">Company Name</flux:heading>
                        <flux:text>{{ $company->name }}</flux:text>
                    </div>

                    @if($company->description)
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-1">Description</flux:heading>
                            <flux:text>{{ $company->description }}</flux:text>
                        </div>
                    @endif

                    @if($company->tax_id)
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-1">Tax ID</flux:heading>
                            <flux:text>{{ $company->tax_id }}</flux:text>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <flux:heading size="lg" class="mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Contact Information
                </flux:heading>

                <div class="space-y-4">
                    <div>
                        <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-1">Email</flux:heading>
                        <flux:text>{{ $company->email }}</flux:text>
                    </div>

                    <div>
                        <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-1">Phone</flux:heading>
                        <flux:text>{{ $company->phone }}</flux:text>
                    </div>

                    @if($company->website)
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-1">Website</flux:heading>
                            <flux:text>
                                <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $company->website }}
                                </a>
                            </flux:text>
                        </div>
                    @endif

                    @if(!empty($socialMedia))
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-2">Social Media</flux:heading>
                            <div class="flex flex-wrap gap-2">
                                @foreach($socialMedia as $social)
                                    <a href="{{ $social['url'] }}" target="_blank"
                                       class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                        <span class="capitalize">{{ $social['platform'] }}</span>
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Address Card - Full Width -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                <flux:heading size="lg" class="mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Address
                </flux:heading>

                <div class="prose prose-sm max-w-none dark:prose-invert">
                    {!! nl2br(e($company->address)) !!}
                </div>
            </div>

            <!-- License Information Card - Full Width -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                <flux:heading size="lg" class="mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    License Information
                </flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- License Status -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ $licenseInfo['license_status'] }}</p>
                            </div>
                            <div class="w-3 h-3 rounded-full
                                @if($licenseInfo['license_status'] === 'active' && !$licenseInfo['is_expired']) bg-green-500
                                @elseif($licenseInfo['license_status'] === 'expired' || $licenseInfo['is_expired']) bg-red-500
                                @elseif($licenseInfo['license_status'] === 'suspended') bg-yellow-500
                                @else bg-gray-500
                                @endif">
                            </div>
                        </div>
                        @if($licenseInfo['is_expired'])
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">License has expired</p>
                        @elseif($isLicenseExpiringSoon)
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">Expires in {{ $daysUntilExpiration }} days</p>
                        @endif
                    </div>

                    <!-- License Type -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Type</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $licenseInfo['type_display'] }}</p>
                        @if($licenseInfo['is_trial'])
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Trial version</p>
                        @endif
                    </div>

                    <!-- Expiration Date -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Expires</p>
                        @if($licenseInfo['license_expires_at'])
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $licenseInfo['license_expires_at']->format('M d, Y') }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ $licenseInfo['license_expires_at']->diffForHumans() }}
                            </p>
                        @else
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Never</p>
                        @endif
                    </div>

                    <!-- Max Users -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Max Users</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ $licenseInfo['max_users'] ?? 'Unlimited' }}
                        </p>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="mt-6 space-y-4">
                    @if($licenseInfo['license_key'])
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-1">License Key</flux:heading>
                            <flux:text class="font-mono text-sm">{{ $licenseInfo['license_key'] }}</flux:text>
                        </div>
                    @endif

                    @if($licenseInfo['license_issued_at'])
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-1">Issued Date</flux:heading>
                            <flux:text>{{ $licenseInfo['license_issued_at']->format('F d, Y') }}</flux:text>
                        </div>
                    @endif

                    @if($licenseInfo['max_storage_gb'])
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-1">Storage Limit</flux:heading>
                            <flux:text>{{ $licenseInfo['max_storage_gb'] }} GB</flux:text>
                        </div>
                    @endif

                    @if(!empty($licenseInfo['features_enabled']))
                        <div>
                            <flux:heading size="sm" class="text-gray-600 dark:text-gray-400 mb-2">Enabled Features</flux:heading>
                            <div class="flex flex-wrap gap-2">
                                @foreach($licenseInfo['features_enabled'] as $feature)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- License Actions -->
                @can('company.edit')
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <flux:button variant="outline" size="sm" href="{{ route('company.edit') }}#license-section" wire:navigate icon="pencil-square">
                            Manage License
                        </flux:button>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>
