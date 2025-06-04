@props(['label' => 'Menu', 'items' => []])
<div x-data="{ open: false }" class="relative inline-block" {{ $attributes }}>
    <button type="button" @click="open = !open" class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white rounded shadow">
        {{ $label }}
        <svg class="w-4 h-4 ml-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
    </button>
    <div x-show="open" x-transition @click.outside="open=false" class="absolute right-0 z-50 mt-2 origin-top-right bg-white divide-y divide-gray-100 rounded shadow w-48">
        <ul class="py-1 text-sm text-gray-700">
            @foreach($items as $item)
                <li><a href="{{ $item['href'] }}" class="block px-4 py-2 hover:bg-gray-100">{{ $item['label'] }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
