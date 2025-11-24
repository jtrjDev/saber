@props(['type' => 'submit'])

<button 
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold
                    bg-red-600 text-white shadow-sm
                    hover:bg-red-700 active:bg-red-800
                    focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2
                    transition-all duration-150 ease-in-out'
    ]) }}>
    {{ $slot }}
</button>
