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
        $this->defaultLanguage = $this->get(_settings('main_language',1));
    }

    public function get(int $id): Language
    {
        return $this->languages->where('id', $id)->first();
    }

    public function getMulti(array $ids): Collection
    {
        return $this->languages->whereIn('id', $ids);
    }

    public function getDefault(): Language
    {
        return $this->defaultLanguage;
    }
}
