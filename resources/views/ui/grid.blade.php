@props(['cols' => 1, 'gap' => 4])
<div {{ $attributes->class('grid') }} style="grid-template-columns: repeat({{ $cols }}, minmax(0, 1fr)); gap: {{ $gap }}rem;">
    {{ $slot }}
</div>
