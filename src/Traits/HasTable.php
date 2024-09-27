<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasTable
{
    abstract public static function getDb(): string;

    /**
     * @return void
     */
    protected static function bootHasTable()
    {
        static::booting(function (Model $model) {
            $model->table = self::getDb();
            $model->setTable(self::getDb());
        });
    }
}
