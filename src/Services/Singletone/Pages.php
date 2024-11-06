<?php

namespace SmartCms\Core\Services\Singletone;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Outerweb\Settings\Models\Setting;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Page;

class Pages
{
   public $pages;

   public function __construct()
   {
      $this->pages = config('shared.page_model')::all();
   }

   public function get(int $id): Page
   {
      return $this->pages->where('id', $id)->first();
   }

   public function getMulti(array $ids): Collection
   {
      return $this->pages->whereIn('id', $ids);
   }

   public function first(): Page
   {
      return $this->pages->first();
   }

   public function all(): Collection
   {
      return $this->pages;
   }
}
