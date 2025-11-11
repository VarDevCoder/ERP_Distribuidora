@props([
    'last' => false,
])

<td {{ $attributes->merge(['class' => 'px-6 py-4 ' . ($last ? '' : 'border-r-2 border-gray-400')]) }}>
    {{ $slot }}
</td>
