<?php

namespace SmartCms\Core\Support\Extenders;

class PageExtender
{
    protected static array $properties = [];

    protected static array $microdata = [];

    public function addProperty(string $name, $value): self
    {
        self::$properties[$name] = $value;

        return $this;
    }

    public function getProperties(): array
    {
        return self::$properties;
    }

    public function addMicrodata() {}

    public function getMicrodata(): array
    {
        return self::$microdata;
    }
}
