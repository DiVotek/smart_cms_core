<?php

namespace SmartCms\Core\Traits;

use App\Models\Translate;

trait HasTranslate
{
    public function translatable()
    {
        return $this->morphOne(Translate::class, 'entity');
    }

    public function getTranslatableNameAttribute()
    {
        if (! is_multi_lang()) {
            return $this->attributes['name'];
        }
        $translation = $this->translatable()->where('language_id', current_lang_id())->first();

        return $translation ? $translation->value : $this->attributes['name'];
    }

    public function name()
    {
        return $this->getTranslatableNameAttribute();
    }
}
