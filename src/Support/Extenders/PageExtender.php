<?php

namespace SmartCms\Core\Support\Extenders;

use Closure;
use Illuminate\Database\Eloquent\Model;

class PageExtender
{
    protected static array $properties = [];

    protected static array $arrayProperties = [];

    protected static array $microdata = [];

    public function addProperty(string $name, Closure $value): self
    {
        static::$properties[$name] = $value;

        return $this;
    }

    public function addProperties(Closure $value): self
    {
        static::$arrayProperties[] = $value;

        return $this;
    }

    public function getProperties(Model $model): array
    {
        $properties = [];
        foreach (static::$properties as $name => $value) {
            $properties[$name] = $value($model);
        }
        foreach (static::$arrayProperties as $name => $value) {
            $properties = array_merge($properties, $value($model));
        }

        return $properties;
    }

    public function addMicrodata() {}

    public function getMicrodata(): array
    {
        return static::$microdata;
    }
}
