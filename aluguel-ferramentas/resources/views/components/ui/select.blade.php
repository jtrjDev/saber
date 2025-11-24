@props(['label' => null, 'name'])

<div class="mb-5">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <select 
        name="{{ $name }}" 
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border-gray-300 shadow-sm 
                        focus:border-indigo-500 focus:ring-indigo-500 
                        text-sm py-2 px-3 bg-white'
        ]) }}>
        {{ $slot }}
    </select>

    @error($name)
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
