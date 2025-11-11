@props([
    'color' => 'blue',
])

@php
    $hoverColors = [
        'blue' => 'hover:bg-blue-100',
        'green' => 'hover:bg-green-100',
        'purple' => 'hover:bg-purple-100',
        'indigo' => 'hover:bg-indigo-100',
        'yellow' => 'hover:bg-yellow-100',
    ];

    $hoverColor = $hoverColors[$color] ?? $hoverColors['blue'];
@endphp

<tr class="{{ $hoverColor }} transition-colors border-b-2 border-gray-400">
    {{ $slot }}
</tr>
