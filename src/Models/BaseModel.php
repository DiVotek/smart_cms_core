<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Traits\HasHooks;

abstract class BaseModel extends Model
{
    use HasHooks;

    protected $tablePrefix = 'smart_cms_';

    protected static ?string $extender = null;

    protected static array $externalCasts = [];

    /**
     * Register dynamic cast from outside (modules, services, etc.)
     */
    public static function addExternalCast(string $attribute, string $castType): void
    {
        static::$externalCasts[static::class][$attribute] = $castType;
    }

    public function getTable()
    {

        $table = parent::getTable();

        if (! str_starts_with($table, $this->tablePrefix)) {
            return $this->tablePrefix.$table;
        }

        return $table;
    }

    public function getCasts(): array
    {
        $baseCasts = parent::getCasts();

        $externalCasts = static::$externalCasts[static::class] ?? [];

        return array_merge($baseCasts, $externalCasts);
    }

    public static function getDb(): string
    {
        return (new static)->getTable();
    }

    protected static function booted()
    {
        parent::booted();

        if (static::$extender) {
            app(static::$extender)->apply(new static);
        }

        static::saving(function (BaseModel $model) {
            $model->applyHook('before_save', $model);
        });
        static::creating(function (BaseModel $model) {
            $model->applyHook('before_create', $model);
        });
        static::updating(function (BaseModel $model) {
            $model->applyHook('before_update', $model);
        });
        static::deleting(function (BaseModel $model) {
            $model->applyHook('before_delete', $model);
        });
    }
}
