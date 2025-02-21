<?php

namespace SmartCms\Core\Repositories\Page;

use DateTime;
use SmartCms\Core\Repositories\DtoInterface;
use SmartCms\Core\Traits\Dto\AsDto;

class PageDto implements DtoInterface
{
    use AsDto;

    public int $id;

    public string $name;

    public string $originalName;

    public string $link;

    public string $image;

    public string $heading;

    public string $summary;

    public DateTime $created_at;

    public DateTime $updated_at;

    public array $custom_fields;

    public ?PageDto $parent;

    public function __construct(int $id, string $name, string $originalName, string $link, DateTime $created_at, DateTime $updated_at, string $image = '', string $heading = '', string $summary = '', array $custom_fields = [], ?PageDto $parent = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->originalName = $originalName;
        $this->link = $link;
        $this->image = $this->validateImage($image);
        $this->heading = $heading;
        $this->summary = $summary;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->custom_fields = $custom_fields;
        $this->parent = $parent;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'heading' => $this->heading,
            'link' => $this->link,
            'image' => $this->image,
            'summary' => $this->summary,
            'created_at' => $this->transformDate($this->created_at),
            'updated_at' => $this->transformDate($this->updated_at),
            'custom' => (object) $this->custom_fields,
            'parent' => $this->parent ? $this->parent->get() : null,
            ...$this->extra,
        ];
    }
}
