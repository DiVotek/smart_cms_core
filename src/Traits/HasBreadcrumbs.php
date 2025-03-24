<?php

namespace SmartCms\Core\Traits;

/**
 * Trait HasBreadcrumbs
 */
trait HasBreadcrumbs
{
    /**
     * Get the breadcrumbs for the model.
     */
    abstract public function getBreadcrumbs(): array;
}
