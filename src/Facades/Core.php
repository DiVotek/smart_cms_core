<?php

namespace SmartCms\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SmartCms\Core\Core
 */
class Core extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SmartCms\Core\Core::class;
    }
}
