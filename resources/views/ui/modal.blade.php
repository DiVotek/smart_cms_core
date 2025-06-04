@props(['open' => false, 'title' => null, 'maxWidth' => '32rem'])
<div x-data="{ open: @js($open) }" @keydown.window.escape="open = false" x-cloak {{ $attributes }}>
    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50" @click="open = false" aria-hidden="true"></div>
        <div x-show="open" x-transition class="w-full mx-4 bg-white rounded-lg shadow-lg" style="max-width: {{ $maxWidth }}">
            @if($title)
                <div class="flex items-center justify-between px-4 py-2 border-b">
                    <h2 class="text-lg font-semibold">{{ $title }}</h2>
                    <button @click="open=false" class="text-gray-600 hover:text-gray-800">&times;</button>
                </div>
            @endif
            <div class="p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
