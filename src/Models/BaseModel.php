<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $tablePrefix;

    public function getTable()
    {
        $this->tablePrefix = sconfig('database_table_prefix', 'smart_cms_');

        $table = parent::getTable();

        if (!str_starts_with($table, $this->tablePrefix)) {
            return $this->tablePrefix . $table;
        }

        return $table;
    }

    public static function getDb(): string
    {
        return (new static())->getTable();
    }
}
