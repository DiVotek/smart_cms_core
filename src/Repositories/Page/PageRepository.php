<?php

namespace SmartCms\Core\Repositories\Page;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Seo;
use SmartCms\Core\Repositories\RepositoryInterface;
use SmartCms\Core\Resources\PageResource;
use SmartCms\Core\Services\Schema\SchemaParser;

class PageRepository implements RepositoryInterface
{
    public static function make(): PageRepository
    {
        return new self;
    }

    public function find(int $id): object
    {
        return PageResource::make(Page::query()->find($id))->get();
    }

    public function filterBy(array $filters, array $sort = [], int $limit = 10): array|Collection
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

        return $pages->map(function ($page) {
            return PageResource::make($page)->get();
        });
    }

    public function findMultiple(array $ids): array
    {
        $pages = Page::query()->whereIn('id', $ids)->get();

        return PageResource::collection($pages)->get();
        $pageDtos = [];
        foreach ($pages as $page) {
            $pageDtos[] = $this->transform($page)->toObject();
        }

        return $pageDtos;
    }

    public function transform(Page $page, $isParent = true): PageDto
    {
        $seo = $page->seo ?? new Seo;
        $custom_fields = $page->custom ?? [];
        $custom = [];
        if ($custom_fields && $page->parent) {
            $menuSection = MenuSection::query()->where('parent_id', $page->parent->parent_id ?? $page->parent->id)->first();
            if ($menuSection) {
                $custom = SchemaParser::make($menuSection->custom_fields ?? [], $custom_fields);
            }
        }
        $parent = null;
        if ($isParent) {
            $parent = $page->parent ? $this->transform($page->parent, false) : null;
        }
        $dto = new PageDto(
            $page->id,
            $page->name(),
            $page->name,
            $page->route(),
            $page->created_at,
            $page->updated_at,
            $page->image ?? '',
            $seo->heading ?? $page->name(),
            $seo->summary ?? '',
            $custom,
            $parent
        );
        Event::dispatch('cms.page.transform', [$page, $dto]);

        return $dto;
    }
}
