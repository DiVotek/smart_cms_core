<?php

namespace SmartCms\Core\Traits\Repository;

/**
 * Trait AsRepository
 *
 * @package SmartCms\Core\Traits\Repository
 */
trait AsRepository
{
    /**
     * Create a new instance of the model.
     *
     * @return static
     */
    public static function make(): self
    {
        return new self;
    }
}
