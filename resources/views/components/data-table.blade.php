@props([
    'headers' => [],
    'color' => 'blue', // blue, green, purple, indigo
])

@php
    $gradients = [
        'blue' => 'from-blue-200 to-blue-100',
        'green' => 'from-green-200 to-green-100',
        'purple' => 'from-purple-200 to-purple-100',
        'indigo' => 'from-indigo-200 to-indigo-100',
        'yellow' => 'from-yellow-200 to-yellow-100',
    ];

    $hoverColors = [
        'blue' => 'hover:bg-blue-100',
        'green' => 'hover:bg-green-100',
        'purple' => 'hover:bg-purple-100',
        'indigo' => 'hover:bg-indigo-100',
        'yellow' => 'hover:bg-yellow-100',
    ];

    $gradient = $gradients[$color] ?? $gradients['blue'];
    $hoverColor = $hoverColors[$color] ?? $hoverColors['blue'];
@endphp

<div class="bg-white rounded-lg shadow-lg overflow-hidden border-[3px] border-gray-500">
    <table class="min-w-full border-collapse">
        <thead class="bg-gradient-to-r {{ $gradient }}">
            <tr>
                @foreach($headers as $index => $header)
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-b-[3px] {{ $index < count($headers) - 1 ? 'border-r-2' : '' }} border-gray-500">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
