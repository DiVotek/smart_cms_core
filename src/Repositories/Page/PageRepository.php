<?php

namespace SmartCms\Core\Repositories\Page;

use Illuminate\Support\Collection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Repositories\RepositoryInterface;
use SmartCms\Core\Resources\PageResource;

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
    }
}
