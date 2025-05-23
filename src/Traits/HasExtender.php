<?php

namespace SmartCms\Core\Traits;

trait HasExtender
{
    abstract public static function getExtender(): ?string;

    protected function initializeHasExtender(): void
    {
        if (static::getExtender()) {
            app(static::getExtender())->apply($this);
        }
    }
}
