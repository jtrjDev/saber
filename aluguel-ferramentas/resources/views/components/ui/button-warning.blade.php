@props(['href' => null])

@php
    $classes = 'inline-flex items-center gap-2 px-4 py-2 
                rounded-lg text-sm font-semibold
                bg-yellow-400 text-yellow-900 shadow-sm
                hover:bg-yellow-500 active:bg-yellow-600
                focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2
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
