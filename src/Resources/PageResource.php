<?php

namespace SmartCms\Core\Resources;

class PageResource extends BaseResource
{
    public function prepareData($request): array
    {
        $seo = $this->resource->getSeo();
        $name = $this->resource->name();
        $custom_fields = $this->resource->custom_fields ?? [];

        $data = [
            'id' => $this->id,
            'name' => $name,
            'heading' => $seo->heading ?? $name,
            'link' => $this->resource->route(),
            'image' => $this->validateImage($this->resource->image),
            'summary' => $seo->summary ?? '',
            'created_at' => $this->transformDate($this->created_at),
            'updated_at' => $this->transformDate($this->updated_at),
            'custom' => (object) $custom_fields,
            'parent' => $this->resource->parent_id ? PageResource::make($this->resource->getCachedParent())->get() : null,
        ];

        return $data;
    }
}
