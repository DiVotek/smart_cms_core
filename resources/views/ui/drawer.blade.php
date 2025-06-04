@props(['open' => false, 'position' => 'right', 'width' => '20rem', 'title' => null])
<div x-data="{ open: @js($open) }" x-cloak {{ $attributes }}>
    <div x-show="open" class="fixed inset-0 z-50 flex">
        <div class="absolute inset-0 bg-black/50" @click="open=false" aria-hidden="true"></div>
        <div x-show="open" x-transition class="relative bg-white shadow-lg w-full" :class="{ 'ml-auto': '{{ $position }}' === 'right', 'mr-auto': '{{ $position }}' === 'left' }" style="max-width: {{ $width }}">
            <div class="flex items-center justify-between p-4 border-b" x-show="{{ $title ? 'true' : 'false' }}">
                <h2 class="text-lg font-semibold">{{ $title }}</h2>
                <button @click="open=false" class="text-gray-600 hover:text-gray-800">&times;</button>
            </div>
            <div class="p-4 overflow-y-auto h-full">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
