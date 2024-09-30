<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasViews
{
    protected static function bootHasViews(): void
    {
        static::booting(function (Model $model) {
            $model->mergeFillable([$model->getViewsColumn()]);
        });
    }

    public function getViewsColumn(): string
    {
        return property_exists($this, 'viewsColumn') ? $this->viewsColumn : 'views';
    }

    public function view(): int
    {
        return $this->increment($this->getViewsColumn());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMostViewed(Builder $query)
    {
        return $query->orderBy($this->getViewsColumn(), 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeastViewed(Builder $query)
    {
        return $query->orderBy($this->getViewsColumn(), 'asc');
    }
}
