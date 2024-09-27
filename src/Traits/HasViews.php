<?php

namespace SmartCms\Core\Traits;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

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

    public function viewsMigrationField(Blueprint $table): void
    {
        $table->integer('views')->default(0);
    }

    public function viewsFilamentColumn(): TextColumn
    {
        return TextColumn::make('views')
            ->label(__('Views'))
            ->numeric()
            ->sortable();
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
