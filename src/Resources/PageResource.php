<?php

namespace SmartCms\Core\Resources;

use SmartCms\Core\Models\Seo;

class PageResource extends BaseResource
{
    /**
     * Static in-memory cache for the current request only
     */
    protected static $requestCache = [];

    public function prepareData($request): array
    {
        $cacheKey = $this->getKey();

        if (isset(static::$requestCache[$cacheKey])) {
            return static::$requestCache[$cacheKey];
        }

        $seo = $this->resource->getSeo();
        $name = $this->resource->name();
        $custom_fields = $this->resource->custom_fields ?? [];

        $data = [
            'id' => $this->id,
            'name' => $name,
            'heading' => $seo->heading ?? $name,
            'link' => $this->resource->route(),
            'image' => $this->validateImage($this->resource->image),
            'summary' => $seo->summary ?? '',
            'created_at' => $this->transformDate($this->created_at),
            'updated_at' => $this->transformDate($this->updated_at),
            'custom' => (object) $custom_fields,
            'parent' => $this->resource->parent_id ? PageResource::make($this->resource->parent)->get() : null,
        ];

        // Store in request cache
        static::$requestCache[$cacheKey] = $data;

        return $data;
    }

    public function getKey()
    {
        return 'page_resource_' . $this->resource->id . '_' . current_lang_id();
    }

    /**
     * Clear the resource cache for a specific page
     */
    public static function clearCache($pageId)
    {
        $cacheKey = 'page_resource_' . $pageId . '_' . current_lang_id();
        if (isset(static::$requestCache[$cacheKey])) {
            unset(static::$requestCache[$cacheKey]);
        }
    }

    /**
     * Clear the entire request cache
     */
    public static function clearAllCache()
    {
        static::$requestCache = [];
    }
}
