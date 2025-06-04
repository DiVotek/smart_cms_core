<?php

namespace SmartCms\Core\Traits;

/**
 * Trait HasHooks
 */
trait HasHooks
{
    protected static $hooks = [];

    public static function registerHook($hookName, $callback, $priority = 10)
    {
        $className = static::class;

        if (! isset(static::$hooks[$className][$hookName])) {
            static::$hooks[$className][$hookName] = [];
        }

        // Check if this exact callback already exists
        foreach (static::$hooks[$className][$hookName] as $existingHook) {
            if ($existingHook['callback'] === $callback && $existingHook['priority'] === $priority) {
                return; // Hook already exists, don't add duplicate
            }
        }

        static::$hooks[$className][$hookName][] = [
            'callback' => $callback,
            'priority' => $priority,
        ];

        usort(static::$hooks[$className][$hookName], function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }

    public static function applyHook($hookName, &$value = null, ...$args)
    {
        $className = static::class;
        if (! isset(static::$hooks[$className][$hookName])) {
            return $value;
        }

        foreach (static::$hooks[$className][$hookName] as $hook) {
            $callback = $hook['callback'];
            if (is_string($callback) && class_exists($callback)) {
                // It's a class name with invoke method
                $callbackInstance = new $callback;
                $tempResult = $callbackInstance($value, ...$args);
                if ($tempResult !== null) {
                    $value = $tempResult;
                }
            } elseif (is_array($callback) && count($callback) === 2 && is_string($callback[0]) && class_exists($callback[0])) {
                // It's a [ClassName, methodName] format
                $tempResult = call_user_func($callback, $value, ...$args);
                if ($tempResult !== null) {
                    $value = $tempResult;
                }
            } else {
                // Closure or callable
                $tempResult = $callback($value, ...$args);
                if ($tempResult !== null) {
                    $value = $tempResult;
                }
            }
        }

        return $value;
    }
}
