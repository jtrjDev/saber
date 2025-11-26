@props([
    'title' => '',
    'value' => 0,
    'color' => 'blue',
    'icon' => '📌',
])

@php
    $colors = [
        'blue' => 'border-blue-500 text-blue-600',
        'green' => 'border-green-500 text-green-600',
        'yellow' => 'border-yellow-500 text-yellow-600',
        'orange' => 'border-orange-500 text-orange-600',
        'red' => 'border-red-500 text-red-600',
        'purple' => 'border-purple-500 text-purple-600',
    ];

    $colorClasses = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 {{ $colorClasses }} hover:shadow-xl transition-shadow duration-300 transform hover:scale-105">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">{{ $title }}</p>
            <p class="text-4xl font-bold mt-2 text-gray-900 dark:text-white">
                {{ $value }}
            </p>
        </div>
        <div class="text-4xl">{{ $icon }}</div>
    </div>
    <div class="mt-4 flex items-center text-sm {{ $colorClasses }}">
        <span class="inline-block w-2 h-2 rounded-full mr-2"
              style="background-color: currentColor">
        </span>
        {{ $slot }}
    </div>
</div>
