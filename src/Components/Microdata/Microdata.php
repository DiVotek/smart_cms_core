<?php

namespace SmartCms\Core\Components\Microdata;

use Illuminate\View\Component;

class Microdata extends Component
{
    public string $type;

    public array $properties;

    public function __construct(string $type, array $properties = [])
    {
        $properties = array_filter($properties);
        $this->type = $type;
        $this->properties = $properties;
    }

    public function toJsonLd(): string
    {
        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => $this->type,
        ] + $this->properties, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function render()
    {
        return <<<'blade'
            <script type="application/ld+json">{!! $toJsonLd() !!}</script>
        blade;
    }
}
