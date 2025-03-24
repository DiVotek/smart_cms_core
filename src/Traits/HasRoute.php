<?php

namespace SmartCms\Core\Traits;

/**
 * Trait HasRoute
 *
 * @package SmartCms\Core\Traits
 */
trait HasRoute
{
    /**
     * Get the route for the model.
     *
     * @return string
     */
    abstract public function route(): string;
}
