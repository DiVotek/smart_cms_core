@props(['items' => []])
<nav aria-label="Breadcrumb" {{ $attributes }}>
    <ol class="flex flex-wrap items-center space-x-2 text-sm text-gray-600">
        @foreach($items as $item)
            <li class="flex items-center">
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:underline">{{ $item['label'] }}</a>
                @else
                    <span aria-current="page">{{ $item['label'] }}</span>
                @endif
                @if(!$loop->last)
                    <span class="mx-2">/</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
