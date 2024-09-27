<?php

namespace SmartCms\Core\Traits;

trait HasBreadcrumbs
{
    abstract public function getBreadcrumbs(): array;
}
