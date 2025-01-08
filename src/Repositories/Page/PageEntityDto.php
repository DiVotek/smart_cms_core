<?php

namespace SmartCms\Core\Repositories\Page;

use DateTime;
use SmartCms\Core\Repositories\DtoInterface;
use SmartCms\Core\Traits\Dto\AsDto;

class PageEntityDto implements DtoInterface
{
   use AsDto;

   public string $name;
   public array $breadcrumbs = [];
   public string $title;
   public string $heading;
   public string $short_description;
   public string $content;
   public string $image;
   public string $banner;
   public array $categories = [];
   public array $items = [];
   public DateTime $created_at;
   public DateTime $updated_at;

   public function __construct(string $name, string $image, DateTime $created_at, DateTime $updated_at, array $breadcrumbs = [], string $title = '', string $heading = '', string $short_description = '', string $content = '', string $banner = '', array $categories = [], array $items = [])
   {
      $this->name = $name;
      $this->breadcrumbs = $breadcrumbs;
      $this->title = $title;
      $this->heading = $heading;
      $this->short_description = $short_description;
      $this->content = $content;
      $this->image = $image;
      $this->banner = $banner;
      $this->categories = $categories;
      $this->items = $items;
      $this->created_at = $created_at;
      $this->updated_at = $updated_at;
   }

   public function toArray(): array
   {
      return [
         'name' => $this->name,
         'breadcrumbs' => $this->breadcrumbs,
         'heading' => $this->heading,
         'summary' => $this->short_description,
         'content' => $this->content,
         'image' => $this->image,
         'banner' => $this->banner,
         'categories' => $this->categories,
         'items' => $this->items,
         'created_at' => $this->transformDate($this->created_at),
         'updated_at' => $this->transformDate($this->updated_at),
         ...$this->extra
      ];
   }

}
