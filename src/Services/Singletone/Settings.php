<?php

namespace SmartCms\Core\Services\Singletone;

use Illuminate\Support\Facades\Schema;
use Outerweb\Settings\Models\Setting;

class Settings
{
    protected string $table = 'settings';

    public mixed $settings;

    public function __construct()
    {
        if (Schema::hasTable($this->table)) {
            $this->settings = Setting::all();
        } else {
            $this->settings = collect();
        }
    }

    public function get(string $key): mixed
    {
        return $this->settings->where('key', $key)->first()?->value ?? null;
    }
}
