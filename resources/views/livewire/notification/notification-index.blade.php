<div x-data="{ open: false }" class="relative">
    <button @click="open = !open"
        class="p-2 rounded-full text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200 focus:outline-none cursor-pointer">
        <div class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
    </button>

    <div x-show="open" @click.away="open = false"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-zinc-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-zinc-700">
        <div class="px-4 py-2 border-b border-gray-200 dark:border-zinc-700">
            <p class="text-sm font-medium">Notifications</p>
        </div>
        <div class="max-h-60 overflow-y-auto custom-scrollbar">
            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-zinc-700 border-b border-gray-100 dark:border-zinc-700">
                <p class="text-sm font-medium">System Update</p>
                <p class="text-xs text-gray-500 dark:text-zinc-400">The system will be updated tonight at 2 AM</p>
                <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
            </a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-zinc-700 border-b border-gray-100 dark:border-zinc-700">
                <p class="text-sm font-medium">New User Registered</p>
                <p class="text-xs text-gray-500 dark:text-zinc-400">Jane Doe has registered</p>
                <p class="text-xs text-gray-400 mt-1">5 hours ago</p>
            </a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-zinc-700">
                <p class="text-sm font-medium">Server Alert</p>
                <p class="text-xs text-gray-500 dark:text-zinc-400">High CPU usage detected</p>
                <p class="text-xs text-gray-400 mt-1">1 day ago</p>
            </a>
        </div>
        <div class="px-4 py-2 border-t border-gray-200 dark:border-zinc-700">
            <a href="#" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">View all notifications</a>
        </div>
    </div>
</div>
