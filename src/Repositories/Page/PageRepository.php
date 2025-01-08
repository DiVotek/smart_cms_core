<?php

namespace SmartCms\Core\Repositories\Page;

use Illuminate\Support\Facades\Event;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Seo;
use SmartCms\Core\Repositories\DtoInterface;
use SmartCms\Core\Repositories\RepositoryInterface;

class PageRepository implements RepositoryInterface
{

   public static function make(): PageRepository
   {
      return new self();
   }

   public function find(int $id): object
   {
      $page = Page::query()->find($id);
      return $this->transform($page)->toObject();
   }

   public function filterBy(array $filters, array $sort = [], int $limit = 10): array
   {
      $pages = Page::query();
      foreach ($filters as $key => $value) {
         if (is_array($value)) {
            $pages->whereIn($key, $value);
            continue;
         }
         $pages->where($key, $value);
      }
      foreach ($sort as $key => $value) {
         if ($key == 'random') {
            $pages->inRandomOrder();
            continue;
         }
         $pages->orderBy($key, $value);
      }
      $pages = $pages->limit($limit)->get();
      $pageDtos = [];
      foreach ($pages as $page) {
         $pageDtos[] = $this->transform($page)->toObject();
      }
      return $pageDtos;
   }

   public function findMultiple(array $ids): array
   {
      $pages = Page::query()->whereIn('id', $ids)->get();
      $pageDtos = [];
      foreach ($pages as $page) {
         $pageDtos[] = $this->transform($page)->toObject();
      }
      return $pageDtos;
   }

   public function transform(Page $page): PageDto
   {
      $seo = $page->seo ?? new Seo();
      $dto = new PageDto(
         $page->id,
         $page->name(),
         $page->name,
         $page->route(),
         $page->created_at,
         $page->updated_at,
         $page->image ?? '',
         $page->custom ?? [],
         $seo->heading ?? $page->name(),
         $seo->summary ?? ''
      );
      Event::dispatch('cms.page.transform', [$page, $dto]);

      return $dto;
   }
}
