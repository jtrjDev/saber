@props(['type' => 'submit', 'href' => null])

@php
    $classes = 'inline-flex items-center gap-2 px-4 py-2 
                rounded-lg text-sm font-semibold
                bg-indigo-600 text-white shadow-sm
                hover:bg-indigo-700 active:bg-indigo-800
                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                transition-all duration-150 ease-in-out';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
