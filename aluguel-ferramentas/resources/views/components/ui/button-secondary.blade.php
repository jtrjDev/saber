@props(['href' => null])

@php
    $classes = 'inline-flex items-center gap-2 px-4 py-2 
                rounded-lg text-sm font-semibold
                bg-gray-200 text-gray-800 shadow-sm
                hover:bg-gray-300 active:bg-gray-400
                focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2
                transition-all duration-150 ease-in-out';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
