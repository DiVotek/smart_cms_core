<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSorting
{
    protected static function bootHasSorting(): void
    {
        static::addGlobalScope('sorted', function (Builder $builder) {
            $instance = new static;
            $instance->scopeSorted($builder);
        });
    }

    public function scopeSorted(Builder $query): Builder
    {
        return $query->orderBy($this->getDb().'.'.$this->getSortingColumn(), 'asc');
    }

    public function getSortingColumn(): string
    {
        return property_exists($this, 'sortingColumn') ? $this->sortingColumn : 'sorting';
    }
}
