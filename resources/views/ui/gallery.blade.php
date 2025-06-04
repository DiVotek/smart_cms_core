@props(['images' => []])
<div x-data="{ open: false, current: '' }" {{ $attributes }}>
    <div class="grid grid-cols-2 gap-2 md:grid-cols-3">
        @foreach($images as $img)
            <img src="{{ $img['src'] }}" alt="{{ $img['alt'] ?? '' }}" class="cursor-pointer" @click="open=true; current='{{ $img['src'] }}'">
        @endforeach
    </div>
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/60" @click="open=false" aria-hidden="true"></div>
        <img :src="current" class="relative max-h-[80vh]">
    </div>
</div>
