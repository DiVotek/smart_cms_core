@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md'
])
<button type="{{ $type }}" {{ $attributes->class([
        'px-4 py-2 rounded font-medium focus:outline-none focus:ring',
        'text-white bg-blue-600 hover:bg-blue-700' => $variant === 'primary',
        'text-gray-700 bg-gray-200 hover:bg-gray-300' => $variant === 'secondary',
        'text-white bg-red-600 hover:bg-red-700' => $variant === 'danger',
        'text-sm' => $size === 'sm',
        'text-base' => $size === 'md',
        'text-lg' => $size === 'lg',
    ]) }}>
    {{ $slot }}
</button>
