<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

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
        // static::booting(function (Model $model) {
        //     $model->mergeFillable([$model->getStatusColumn()]);
        // });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(self::getDb().'.'.$this->getStatusColumn(), self::STATUS_ON);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where($this->getStatusColumn(), self::STATUS_OFF);
    }

    public function getStatusColumn(): string
    {
        return property_exists($this, 'statusColumn') ? $this->statusColumn : 'status';
    }

    public function getDefaultStatusValue(): int
    {
        return self::STATUS_ON;
    }

    public function statusMigrationField(Blueprint $table): void
    {
        $defaultStatus = $this->getDefaultStatusValue();
        $table->tinyInteger('status')->default($defaultStatus)->after('id')->index();
    }

    public function fakeStatus(): int
    {
        return rand(0, 1);
    }
}
