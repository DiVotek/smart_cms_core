<?php

namespace SmartCms\Core\Traits;

use SmartCms\Core\Models\Seo;

trait HasSeo
{
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }

    public function addSeo(array $attributes)
    {
        return $this->seo()->create($attributes);
    }

    public function updateSeo(array $attributes)
    {
        return $this->seo()->update($attributes);
    }
}
