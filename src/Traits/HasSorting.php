<?php

namespace SmartCms\Core\Traits;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

trait HasSorting
{
    public function initializeHasSorting()
    {
        $this->mergeFillable([$this->getSortingColumn()]);
    }

    protected static function bootHasSorting(): void
    {
        static::addGlobalScope('sorted', function (Builder $builder) {
            $instance = new static;
            $instance->scopeSorted($builder);
        });
        static::booting(function (Model $model) {
            $model->mergeFillable([$model->getSortingColumn()]);
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
