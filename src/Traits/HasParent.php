<?php

namespace SmartCms\Core\Traits;

use SmartCms\Core\Services\StaticCache;

/**
 * Trait HasParent
 *
 * @package SmartCms\Core\Traits
 */
trait HasParent
{
    /**
     * Get the parent relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    abstract public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo;

    /**
     * Get the cached parent relationship.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getCachedParent()
    {
        $parentId = $this->parent_id;

        if (! $parentId) {
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
