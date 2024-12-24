<?php

namespace SmartCms\Core\Services\Singletone;

use SmartCms\Core\Models\Translation;

class Translates
{
    public $translates;

    public function __construct()
    {
        $this->translates = Translation::query()->where('language_id', current_lang_id())->get();
    }

    public function get(string $key): string
    {
        $translate = $this->translates->where('key', $key)->first();
        if(!$translate){
            return $key;
        }
        return $translate->value ?? $key;
    }

}
