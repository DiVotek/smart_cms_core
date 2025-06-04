@props(['maxWidth' => '7xl'])
<div {{ $attributes->class('mx-auto px-4') }} style="max-width: {{ $maxWidth }};">
    {{ $slot }}
</div>
