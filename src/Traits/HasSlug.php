<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasSlug
 */
trait HasSlug
{
    public function getSlugColumn(): string
    {
        return property_exists($this, 'slugColumn') ? $this->slugColumn : 'slug';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSlug(Builder $query, string $slug)
    {
        return $query->where($this->getSlugColumn(), $slug);
    }
}
