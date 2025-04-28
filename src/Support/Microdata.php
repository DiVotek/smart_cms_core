<?php

namespace SmartCms\Core\Support;

abstract class Microdata
{
    abstract public static function type(): string;

    public function __construct(public array $properties = []) {}

    abstract public function build(): array;

    public function toJsonLd(): string
    {
        return json_encode($this->build(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function make(array $properties = []): void
    {
        $data = (new static($properties))->build();
        app('seo')->addMicrodata(static::type(), $data);
    }
}
