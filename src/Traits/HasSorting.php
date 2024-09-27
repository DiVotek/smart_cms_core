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

    /**
     * @return void
     */
    public function sortingMigrationField(Blueprint $table)
    {
        $table->integer($this->getSortingColumn())->default(0)->index();
    }

    public function sortingFilamentField(): TextInput
    {
        return TextInput::make($this->getSortingColumn())
            ->label(__('Sorting'))
            ->helperText(__('The sorting for this record'))
            ->numeric()
            ->default(0);
    }

    public function fakeSorting(): int
    {
        return rand(0, 10);
    }
}
