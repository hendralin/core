{{-- resources/views/components/table-row.blade.php --}}
@props(['selectable' => false])

<tr {{ $attributes->merge(['class' => 'odd:bg-white odd:dark:bg-zinc-900 even:bg-gray-50 even:dark:bg-zinc-800 hover:bg-gray-100 dark:border-zinc-700 dark:hover:bg-gray-700/50']) }}>
    @if($selectable)
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-zinc-700 rounded">
        </td>
    @endif
    {{ $slot }}
</tr>
