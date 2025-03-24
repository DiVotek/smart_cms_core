<?php

namespace SmartCms\Core\Traits;

use SmartCms\Core\Models\Seo;
use SmartCms\Core\Services\StaticCache;

/**
 * Trait HasSeo
 *
 * @package SmartCms\Core\Traits
 */
trait HasSeo
{
    protected static $requestCache = [];

    /**
     * Get the SEO relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    /**
     * Get the SEO for the current entity.
     *
     * @param int|null $languageId The language ID to get the SEO for.
     * @return \SmartCms\Core\Models\Seo
     */
    public function getSeo($languageId = null)
    {
        $languageId = $languageId ?? current_lang_id();

        $namespace = self::class . '.seo';
        $key = $this->getKey();
        if (StaticCache::has($namespace, $key)) {
            return StaticCache::get($namespace, $key);
        }
        $seo = $this->seo()->where('language_id', $languageId)->first() ?: new Seo;
        StaticCache::put($namespace, $key, $seo);

        return $seo;
    }
}
