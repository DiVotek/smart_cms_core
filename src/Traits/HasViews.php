<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Trait HasViews
 *
 * @package SmartCms\Core\Traits
 */
trait HasViews
{
    /**
     * Get the views column name.
     *
     * @return string
     */
    public function getViewsColumn(): string
    {
        return property_exists($this, 'viewsColumn') ? $this->viewsColumn : 'views';
    }

    /**
     * Increment the views count.
     *
     * @return int The new views count.
     */
    public function view(): int
    {
        // quietly update dont work with incrementQuietly
        return DB::table(static::getDb())->where('id', $this->id)->increment($this->getViewsColumn());
    }

    /**
     * Scope to order the results by views count in descending order.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder.
     */
    public function scopeMostViewed(Builder $query)
    {
        return $query->orderBy($this->getViewsColumn(), 'desc');
    }

    /**
     * Scope to order the results by views count in ascending order.
     *
     * @param Builder $query The query builder instance.
     * @return Builder The modified query builder.
     */
    public function scopeLeastViewed(Builder $query)
    {
        return $query->orderBy($this->getViewsColumn(), 'asc');
    }
}
