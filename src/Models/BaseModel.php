<?php

namespace SmartCms\Core;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $tablePrefix;

    public function getTable()
    {
        $this->tablePrefix = config('smart_cms.database_table_prefix', 'smart_cms_');
        if (isset($this->table)) {
            return $this->tablePrefix.$this->table;
        }

        return $this->tablePrefix.parent::getTable();
    }

    public static function getDb(): string
    {
        return self::new()->getTable();
    }
}
