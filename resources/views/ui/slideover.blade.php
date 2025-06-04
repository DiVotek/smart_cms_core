@props(['open' => false, 'side' => 'right', 'width' => '20rem', 'title' => null])
<div x-data="{ open: @js($open) }" x-cloak {{ $attributes }}>
    <div x-show="open" class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 bg-black/50" @click="open=false" aria-hidden="true"></div>
        <div x-show="open" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="absolute top-0 bottom-0 bg-white shadow w-full" :class="{ 'right-0': '{{ $side }}' === 'right', 'left-0': '{{ $side }}' === 'left' }" style="width: {{ $width }}">
            @if($title)
                <div class="flex items-center justify-between p-4 border-b">
                    <h2 class="text-lg font-semibold">{{ $title }}</h2>
                    <button @click="open=false" class="text-gray-600 hover:text-gray-800">&times;</button>
                </div>
            @endif
            <div class="p-4 overflow-y-auto h-full">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
