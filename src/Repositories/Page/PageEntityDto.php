<?php

namespace SmartCms\Core\Repositories\Page;

use DateTime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SmartCms\Core\Repositories\DtoInterface;
use SmartCms\Core\Traits\Dto\AsDto;

class PageEntityDto implements DtoInterface
{
    use AsDto;

    public function __construct(public string $name, public ?string $image, public DateTime $created_at, public DateTime $updated_at, public array $breadcrumbs, public LengthAwarePaginator $categories, public LengthAwarePaginator $items, public ?string $heading, public ?string $short_description, public ?string $content, public ?string $banner, public array $siblings, public ?PageDto $parent) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'breadcrumbs' => array_map(fn($breadcrumb) => (object) $breadcrumb, $this->breadcrumbs),
            'heading' => $this->heading ?? $this->name,
            'parent' => $this->parent ? $this->parent->get() : null,
            'siblings' => $this->siblings,
            'summary' => $this->short_description ?? '',
            'content' => $this->content ?? '',
            'image' => $this->validateImage($this->image ?? no_image()),
            'banner' => $this->validateImage($this->banner ?? no_image()),
            'categories' => $this->categories,
            'items' => $this->items,
            'created_at' => $this->transformDate($this->created_at),
            'updated_at' => $this->transformDate($this->updated_at),
            ...$this->extra,
        ];
    }
}
