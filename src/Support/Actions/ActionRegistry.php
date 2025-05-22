<?php

namespace SmartCms\Core\Support\Actions;

use Livewire\Component;

class ActionRegistry
{
    public static array $actions = [];

    public static function resolve(string $name, array $params, Component $instance): ?Action
    {
        if (isset(self::$actions[$name])) {
            return new self::$actions[$name]($name, $params, $instance);
        }

        return null;
    }

    public static function register(string $name, Action $action)
    {
        self::$actions[$name] = $action;
    }

    public static function unregister(string $name)
    {
        unset(self::$actions[$name]);
    }

    public static function getActions()
    {
        return self::$actions;
    }

    public function add(string $name, string $class): self
    {
        self::$actions[$name] = $class;

        return $this;
    }
}
