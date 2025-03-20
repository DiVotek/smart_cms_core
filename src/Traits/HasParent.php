<?php

namespace SmartCms\Core\Traits;

use SmartCms\Core\Services\StaticCache;

trait HasParent
{
    abstract public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo;

    public function getCachedParent()
    {
        $parentId = $this->parent_id;

        if (!$parentId) {
            return null;
        }
        if (StaticCache::has(self::class . '.parent', $parentId)) {
            return StaticCache::get(self::class . '.parent', $parentId);
        }

        $parent = $this->parent;
        StaticCache::put(self::class . '.parent', $parentId, $parent);

        return $parent;
    }
}
