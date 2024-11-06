<?php

namespace SmartCms\Core\Services\Singletone;

use Illuminate\Support\Facades\DB;
use Outerweb\Settings\Models\Setting;

class Settings
{
   protected string $table = 'settings';
   public mixed $settings;
   public function __construct()
   {
      $this->settings = Setting::all();
   }

   public function get(string $key): mixed
   {
      return $this->settings->where('key', $key)->first()?->value;
   }
}
