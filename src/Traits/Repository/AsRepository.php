<?php

namespace SmartCms\Core\Traits\Repository;

trait AsRepository
{
    public static function make(): self
    {
        return new self;
    }
}
