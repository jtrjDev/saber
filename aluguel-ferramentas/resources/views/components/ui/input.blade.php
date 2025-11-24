@props(['label' => null, 'name', 'type' => 'text'])

@php
    $value = old($name, $attributes->get('value'));
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium mb-1">
            {{ $label }}
        </label>
    @endif

    <input 
        name="{{ $name }}"
        id="{{ $name }}"
        type="{{ $type }}"
        value="{{ $value }}"
        {!! $attributes->merge([
            'class' => 'w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500'
        ]) !!}
    >

    @error($name)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
