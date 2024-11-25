<?php

namespace SmartCms\Core\Traits;

use Illuminate\Support\Facades\Context;
use SmartCms\Core\Models\Translate;

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
        if (Context::has($this->get_translate_key())) {
            return Context::get($this->get_translate_key());
        }
        $translation = $this->translatable()->where('language_id', current_lang_id())->first();
        if ($translation) {
            Context::add($this->get_translate_key(), $translation->value);
        } else {
            Context::add($this->get_translate_key(), $this->attributes['name']);
        }

        return Context::get($this->get_translate_key());

        // return $translation ? $translation->value : $this->attributes['name'];
    }

    public function get_translate_key()
    {
        return $this->getTable().'_'.$this->id;
    }

    public function name()
    {
        return $this->getTranslatableNameAttribute();
    }
}
