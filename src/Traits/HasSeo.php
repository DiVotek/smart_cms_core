<?php

namespace SmartCms\Core\Traits;

use Illuminate\Support\Facades\Cache;
use SmartCms\Core\Models\Seo;
use SmartCms\Core\Services\StaticCache;

trait HasSeo
{
    /**
     * Static in-memory cache for the current request only
     */
    protected static $requestCache = [];

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function getSeo($languageId = null)
    {
        $languageId = $languageId ?? current_lang_id();

        $namespace = self::class.'.seo';
        $key = $this->getKey();
        if (StaticCache::has($namespace, $key)) {
            return StaticCache::get($namespace, $key);
        }
        $seo = $this->seo()->where('language_id', $languageId)->first() ?: new Seo;
        StaticCache::put($namespace, $key, $seo);

        return $seo;
    }

    public function addSeo(array $attributes)
    {
        // Clear any cached SEO data when adding
        $languageId = $attributes['language_id'] ?? current_lang_id();
        $cacheKey = 'seo_'.$this->getTable().'_'.$this->getKey().'_'.$languageId;

        if (isset(static::$requestCache[$cacheKey])) {
            unset(static::$requestCache[$cacheKey]);
        }

        return $this->seo()->create($attributes);
    }

    public function updateSeo(array $attributes)
    {
        // Clear any cached SEO data when updating
        $languageId = $attributes['language_id'] ?? current_lang_id();
        $cacheKey = 'seo_'.$this->getTable().'_'.$this->getKey().'_'.$languageId;

        if (isset(static::$requestCache[$cacheKey])) {
            unset(static::$requestCache[$cacheKey]);
        }

        return $this->seo()->update($attributes);
    }

    /**
     * Get SEO fields for the current entity (title, description, etc.)
     */
    public function getSeoFields($languageId = null)
    {
        $seo = $this->getSeo($languageId);
        $name = $this->name ?? $this->title ?? '';

        return [
            'title' => $seo->title ?? $name,
            'heading' => $seo->heading ?? $name,
            'description' => $seo->description ?? '',
            'summary' => $seo->summary ?? '',
            'keywords' => $seo->keywords ?? '',
            'canonical' => $seo->canonical ?? null,
        ];
    }

    /**
     * Clear the entire request cache
     */
    public static function clearSeoCache()
    {
        static::$requestCache = [];
    }
}
