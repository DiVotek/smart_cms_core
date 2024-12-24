<?php

namespace SmartCms\Core\Services\Singletone;

use Illuminate\Support\Collection;
use SmartCms\Core\Models\Language;

class Languages
{
    public mixed $languages;

    public Language $defaultLanguage;

    public function __construct()
    {
        $this->languages = Language::all();
        $this->defaultLanguage = $this->get(_settings('main_language', 1));
    }

    public function get(int $id): Language
    {
        return $this->languages->where('id', $id)->first();
    }

    public function getMulti(array $ids): Collection
    {
        return $this->languages->whereIn('id', $ids)->sort(function ($a, $b) {
            $main_lang = main_lang_id();
            if ($a->id === $main_lang && $b->id !== $main_lang) {
                return -1;
            }
            if ($b->id === $main_lang && $a->id !== $main_lang) {
                return 1;
            }

            return $a->id <=> $b->id;
        })->values();
    }

    public function getDefault(): Language
    {
        return $this->defaultLanguage;
    }
}
