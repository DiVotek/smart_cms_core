<?php

namespace SmartCms\Core\Services\Singletone;

use Illuminate\Support\Collection;
use SmartCms\Core\Models\Translation;

class Translates
{
    public ?Collection $translates = null;

    public function __construct() {}

    public function get(string $key): string
    {
        if (! $this->translates) {
            $this->translates = Translation::query()->where('language_id', current_lang_id())->get();
        }

        $translate = $this->translates->where('key', $key)->first();
        if (! $translate) {
            return $key;
        }

        return $translate->value ?? $key;
    }
}
