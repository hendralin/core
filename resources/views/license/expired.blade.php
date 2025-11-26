<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>License Expired - {{ config('app.name') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Styles -->
    <style>
        .bg-gradient-expired {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .bg-gradient-support {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-zinc-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white dark:bg-zinc-800 rounded-lg shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-expired px-6 py-8 text-center text-white">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-2">License Expired</h1>
            <p class="text-red-100">Your software license has expired</p>
        </div>

        <!-- Content -->
        <div class="px-6 py-8">
            <!-- Company Info -->
            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-zinc-100 mb-2">
                    {{ \App\Models\Company::first()?->name ?? config('app.name') }}
                </h2>
                <p class="text-gray-600 dark:text-zinc-400">
                    Software as a Service Platform
                </p>
            </div>

            <!-- Error Message -->
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- License Details -->
            @php
                $company = \App\Models\Company::first();
            @endphp

            @if($company)
                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-gray-900 dark:text-zinc-100 mb-3">License Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-zinc-400">License Type:</span>
                            <span class="font-medium">{{ $company->getLicenseTypeDisplay() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-zinc-400">Expired Date:</span>
                            <span class="font-medium text-red-600">
                                {{ $company->license_expires_at ? $company->license_expires_at->format('M d, Y') : 'N/A' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-zinc-400">License Key:</span>
                            <span class="font-mono text-xs">{{ $company->license_key ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Required -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <h3 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Action Required</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">
                    To continue using this software, you need to renew your license. Please contact our support team to upgrade or renew your subscription.
                </p>
                <div class="space-y-2">
                    <div class="flex items-center text-sm text-blue-700 dark:text-blue-300">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email: <a href="mailto:info@invyra.cloud" class="underline hover:no-underline ml-1">info@invyra.cloud</a>
                    </div>
                    <div class="flex items-center text-sm text-blue-700 dark:text-blue-300">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Phone: <a href="tel:+6285267008081" class="underline hover:no-underline ml-1">+6285267008081</a>
                    </div>
                </div>
            </div>

            <!-- Support Button -->
            <div class="text-center">
                <a href="mailto:info@invyra.cloud?subject=License Renewal Request&body=Please help me renew my software license.%0D%0A%0D%0ACompany: {{ \App\Models\Company::first()?->name ?? 'N/A' }}%0D%0ALicense Key: {{ \App\Models\Company::first()?->license_key ?? 'N/A' }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-support text-white font-medium rounded-lg hover:shadow-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Contact Support
                </a>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 pt-6 border-t border-gray-200 dark:border-zinc-700">
                <p class="text-xs text-gray-500 dark:text-zinc-400">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <!-- Emergency Access (for development/testing) -->
    @if(config('app.env') === 'local')
        <div class="fixed bottom-4 right-4">
            <button onclick="document.getElementById('emergency-modal').classList.remove('hidden')"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Emergency Access
            </button>
        </div>

        <!-- Emergency Modal -->
        <div id="emergency-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-zinc-100">Emergency Access</h3>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mb-4">
                    This is for development/testing purposes only. Use with caution.
                </p>
                <div class="flex space-x-3">
                    <button onclick="window.location.href='{{ url('/login') }}'"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Go to Login
                    </button>
                    <button onclick="document.getElementById('emergency-modal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 dark:border-zinc-600 text-gray-700 dark:text-zinc-300 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</body>
</html>
