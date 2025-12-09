<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Version History') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Riwayat perubahan dan fitur WOTO') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <!-- Changelog -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6">
        <flux:heading size="lg" class="mb-4 flex items-center gap-2">
            <flux:icon.clock class="h-6 w-6 text-orange-600 dark:text-orange-400" />
            Version History
        </flux:heading>

        <div class="space-y-4">
            @foreach($changeLogs as $changeLog)
            <div class="border-l-4 border-{{ $changeLog['color'] }}-500 pl-4">
                <div class="flex items-center gap-2 mb-2">
                    <flux:text class="font-semibold text-{{ $changeLog['color'] }}-600 dark:text-{{ $changeLog['color'] }}-400">{{ $changeLog['version'] }} - {{ $changeLog['title'] }}</flux:text>
                    @if($changeLog['date'] === 'Latest')
                        <span class="px-2 py-1 bg-{{ $changeLog['color'] }}-100 dark:bg-{{ $changeLog['color'] }}-900 text-{{ $changeLog['color'] }}-800 dark:text-{{ $changeLog['color'] }}-200 text-xs rounded-full">{{ $changeLog['date'] }}</span>
                    @endif
                </div>
                <div class="space-y-1 text-sm text-gray-600 dark:text-zinc-400">
                    @foreach($changeLog['features'] as $feature)
                    <flux:text>{{ $feature }}</flux:text>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 mb-2">
            {{ $changeLogs->links() }}
        </div>
    </div>
</div>
