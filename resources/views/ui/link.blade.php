@props(['href' => '#', 'target' => null])
<a href="{{ $href }}" @if($target) target="{{ $target }}" @endif {{ $attributes->class('text-blue-600 underline hover:text-blue-800') }}>
    {{ $slot }}
</a>
