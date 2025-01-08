@foreach ($advantages as $advantage)
    <x-s::image :src="$advantage->icon" class="w-24 h-24 mx-auto" />
    <span>{{ $advantage->title }}</span>
@endforeach
