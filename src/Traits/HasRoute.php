<?php

namespace SmartCms\Core\Traits;

/**
 * Trait HasRoute
 */
trait HasRoute
{
    /**
     * Get the route for the model.
     */
    abstract public function route(): string;
}
