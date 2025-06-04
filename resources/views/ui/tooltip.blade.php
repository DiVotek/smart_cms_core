@props(['text' => '', 'position' => 'top'])
<span x-data="{ open: false }" class="relative" {{ $attributes }}>
    <span @mouseenter="open = true" @mouseleave="open = false">
        {{ $slot }}
    </span>
    <span x-cloak x-show="open" x-transition class="absolute z-50 px-2 py-1 text-xs text-white bg-black rounded"
        :class="{
            'bottom-full mb-1 left-1/2 -translate-x-1/2': '{{ $position }}' === 'top',
            'top-full mt-1 left-1/2 -translate-x-1/2': '{{ $position }}' === 'bottom'
        }">
        {{ $text }}
    </span>
</span>
