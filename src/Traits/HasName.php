<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

trait HasName
{
    protected static function bootHasName(): void
    {
        static::booting(function (Model $model) {
            $model->mergeFillable([$model->getNameColumn()]);
        });
    }

    public function getNameColumn(): string
    {
        return property_exists($this, 'nameColumn') ? $this->nameColumn : 'name';
    }

    public static function nameMigrationField(Blueprint $table): void
    {
        $table->string('name');
    }
}
