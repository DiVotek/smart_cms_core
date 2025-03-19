<?php

namespace SmartCms\Core\Resources;

use SmartCms\Core\Models\Page;

class PageEntityResource extends BaseResource
{
    public $categories = [];

    public $items = [];

    public $siblings = [];

    public function prepareData($request): array
    {
        $seo = $this->resource->getSeo();
        $name = $this->resource->name();
        $custom_fields = $this->resource->custom_fields ?? [];

        // Get categories and items separately
        $this->fetchCategoriesAndItems($this->resource);
        $this->getSiblings();

        return [
            'id' => $this->id,
            'name' => $name,
            'breadcrumbs' => array_map(fn ($breadcrumb) => (object) $breadcrumb, $this->resource->getBreadcrumbs()),
            'heading' => $seo->heading ?? $name,
            'link' => $this->resource->route(),
            'image' => $this->validateImage($this->resource->image),
            'banner' => $this->validateImage($this->resource->banner),
            'summary' => $seo->summary ?? '',
            'content' => $this->content ?? '',
            'created_at' => $this->transformDate($this->created_at),
            'updated_at' => $this->transformDate($this->updated_at),
            'custom' => (object) $custom_fields,
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

    protected function fetchCategoriesAndItems(Page $entity, $itemsPerPage = 10)
    {
        // Get direct children as categories
        $categories = Page::where('parent_id', $entity->id)
            ->orderBy('sorting')
            ->get();

        // Transform categories
        $this->categories = $categories->map(function (Page $category) {
            return PageResource::make($category)->get();
        });

        // Get all items that belong to any category (for blog-style display)
        // This includes all "posts" regardless of which category they belong to
        $categoryIds = $categories->pluck('id')->toArray();

        if (! empty($categoryIds)) {
            // Get all items from all categories
            $allItems = Page::whereIn('parent_id', $categoryIds)
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform items
            $this->items = $allItems->map(function (Page $page) {
                return PageResource::make($page)->get();
            });
        } else {
            $this->items = collect([]);
        }
    }

    // Add a method specifically for paginated items
    public function getPaginatedItems($itemsPerPage = 10)
    {
        $categories = Page::where('parent_id', $this->resource->id)
            ->orderBy('sorting')
            ->get();

        $categoryIds = $categories->pluck('id')->toArray();

        if (! empty($categoryIds)) {
            // Get paginated items
            $paginatedItems = Page::whereIn('parent_id', $categoryIds)
                ->orderBy('created_at', 'desc')
                ->paginate($itemsPerPage);

            // Transform while preserving pagination
            $items = PageResource::collection($paginatedItems);

            // Get complete response with pagination metadata
            return $items->response()->getData(true);
        }

        return ['data' => [], 'meta' => [], 'links' => []];
    }
}
