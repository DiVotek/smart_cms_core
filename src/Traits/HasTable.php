<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasTable
 *
 * @package SmartCms\Core\Traits
 */
trait HasTable
{
    /**
     * Get the database table name.
     *
     * @return string
     */
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
