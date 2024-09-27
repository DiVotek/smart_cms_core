<?php

namespace SmartCms\Core\Services;

class Status
{
    public const OFF = 0;

    public const ON = 1;

    public const STATUSES = [
        self::ON => 'Включено',
        self::OFF => 'Выключено',
    ];

    public static function getStatuses(): array
    {
        return array_map(function ($el) {
            return __($el);
        }, self::STATUSES);
    }
}
