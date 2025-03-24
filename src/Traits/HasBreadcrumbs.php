<?php

namespace SmartCms\Core\Traits;

/**
 * Trait HasBreadcrumbs
 *
 * @package SmartCms\Core\Traits
 */
trait HasBreadcrumbs
{
    /**
     * Get the breadcrumbs for the model.
     *
     * @return array
     */
    abstract public function getBreadcrumbs(): array;
}
