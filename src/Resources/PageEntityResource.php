<?php

namespace SmartCms\Core\Resources;

use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema\SchemaParser;

class PageEntityResource extends BaseResource
{
    public $categories = [];

    public $items = [];

    public $siblings = [];

    public function prepareData($request): array
    {
        $seo = $this->resource->getSeo();
        $name = $this->resource->name();
        $custom_fields = $this->resource->custom ?? [];
        $custom = [];
        if ($custom_fields && $this->resource->parent_id) {
            $parent = $this->resource->parent;
            $menuSection = MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->first();
            if ($menuSection) {
                $custom = SchemaParser::make($menuSection->custom_fields ?? [], $custom_fields);
            }
        }

        $this->fetchCategoriesAndItems($this->resource);
        $this->getSiblings();
        return [
            'id' => $this->id,
            'name' => $name,
            'breadcrumbs' => array_map(fn($breadcrumb) => (object) $breadcrumb, $this->resource->getBreadcrumbs()),
            'heading' => $seo->heading ?? $name,
            'link' => $this->resource->route(),
            'image' => $this->validateImage($this->resource->image),
            'banner' => $this->validateImage($this->resource->banner),
            'summary' => $seo->summary ?? '',
            'content' => $seo->content ?? '',
            'created_at' => $this->transformDate($this->created_at),
            'updated_at' => $this->transformDate($this->updated_at),
            'custom' => (object) $custom,
            'parent' => $this->resource->parent ? PageResource::make($this->resource->parent)->get() : null,
            'siblings' => $this->siblings,
            'categories' => $this->categories,
            'title' => $seo->title ?? $name,
            'meta_description' => $seo->description ?? '',
            'items' => $this->items,
        ];
    }

    public function getSiblings()
    {
        if ($this->resource->parent_id) {
            $this->siblings = Page::query()->where('parent_id', $this->resource->parent_id)->where('id', '!=', $this->resource->id)->limit(10)->get()->transform(function (Page $page) {
                return PageResource::make($page)->get();
            });
        }
    }

    public function transformCollection($collection)
    {
        return $collection->map(function (Page $page) {
            return PageResource::make($page)->get();
        })->toArray();
    }

    protected function fetchCategoriesAndItems(Page $entity, $itemsPerPage = 15)
    {
        $categories = Page::where('parent_id', $entity->id)
            ->get();

        $this->categories = $categories->map(function (Page $category) {
            return PageResource::make($category)->get();
        });

        $categoryIds = $categories->pluck('id')->toArray();

        if (! empty($categoryIds)) {
            $allItems = Page::whereIn('parent_id', $categoryIds)
                ->paginate($itemsPerPage);

            $this->items = $allItems->through(function (Page $page) {
                return PageResource::make($page)->get();
            });
        } else {
            $this->items = collect([]);
        }
    }
}
