<?php

namespace SmartCms\Core\Services;

class StaticCache
{
    protected static $caches = [];

    public static function get($namespace, $key, $default = null)
    {
        return static::$caches[$namespace][$key] ?? $default;
    }

    public static function has($namespace, $key)
    {
        return isset(static::$caches[$namespace][$key]);
    }

    public static function put($namespace, $key, $value)
    {
        static::$caches[$namespace][$key] = $value;

        return $value;
    }

    public static function forget($namespace, $key = null)
    {
        if ($key === null) {
            unset(static::$caches[$namespace]);
        } else {
            unset(static::$caches[$namespace][$key]);
        }
    }

    public static function clear()
    {
        static::$caches = [];
    }
}
