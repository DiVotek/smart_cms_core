<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasStatus
 */
trait HasStatus
{
    public const STATUS_ON = 1;

    public const STATUS_OFF = 0;

    protected static function bootHasStatus(): void
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $instance = new static;
            $instance->scopeActive($builder);
        });
    }

    /**
     * Scope to filter active records.
     *
     * @param  Builder  $query  The query builder instance.
     * @return Builder The modified query builder.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(self::getDb().'.'.$this->getStatusColumn(), self::STATUS_ON);
    }

    /**
     * Scope to filter inactive records.
     *
     * @param  Builder  $query  The query builder instance.
     * @return Builder The modified query builder.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where($this->getStatusColumn(), self::STATUS_OFF);
    }

    public function getStatusColumn(): string
    {
        return property_exists($this, 'statusColumn') ? $this->statusColumn : 'status';
    }
}
