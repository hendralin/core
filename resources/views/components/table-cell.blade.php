{{-- resources/views/components/table-cell.blade.php --}}
<td {{ $attributes->merge(['class' => 'px-6 py-2 whitespace-nowrap text-sm text-gray-600 dark:text-zinc-400']) }}>
    {{ $slot }}
</td>
