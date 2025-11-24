@props(['label' => null, 'name'])

<div class="mb-4">
    @if($label)
        <label class="block text-sm font-medium mb-1">{{ $label }}</label>
    @endif

    <textarea 
        name="{{ $name }}"
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
        rows="4"
        {{ $attributes }}
    >{{ $slot }}</textarea>

    @error($name)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
