<?php

namespace SmartCms\Core\Traits;

use Illuminate\Support\Facades\Context;
use SmartCms\Core\Models\Translate;

/**
 * Trait HasTranslate
 */
trait HasTranslate
{
    /**
     * Get the translatable relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function translatable()
    {
        return $this->morphOne(Translate::class, 'entity');
    }

    /**
     * Get the translatable name attribute.
     *
     * @return string
     */
    public function getTranslatableNameAttribute()
    {
        if (! is_multi_lang()) {
            return $this->attributes['name'] ?? '';
        }
        if (Context::has($this->get_translate_key())) {
            $translate = Context::get($this->get_translate_key());
            if (!blank($translate)) {
                return $translate;
            }
        }
        $value = $this->attributes['name'] ?? '';
        $translation = $this->translatable()->where('language_id', current_lang_id())->first();
        if ($translation && !blank($translation->value)) {
            $value = $translation->value;
        }
        Context::add($this->get_translate_key(), $value);

        return $value;
    }

    /**
     * Get the translatable key.
     *
     * @return string
     */
    public function get_translate_key()
    {
        return $this->getTable() . '_' . $this->id;
    }

    /**
     * Get the translatable name attribute.
     *
     * @return string
     */
    public function name()
    {
        return $this->getTranslatableNameAttribute();
    }
}
