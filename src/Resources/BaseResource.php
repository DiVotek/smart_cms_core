<?php

namespace SmartCms\Core\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use SmartCms\Core\Traits\HasHooks;

abstract class BaseResource extends JsonResource
{
    use HasHooks;

    protected function applyDataHooks(array $data)
    {
        return $this->applyHook('transform.data', $data, $this->resource);
    }

    public function toArray($request)
    {
        $data = $this->prepareData($request);

        return $this->applyDataHooks($data);
    }

    public function get()
    {
        return (object) $this->toArray(request: request());
    }

    abstract protected function prepareData($request): array;

    public function validateImage(?string $image): string
    {
        return validateImage($image ?? no_image());
    }

    public function transformDate($date): string
    {
        $date = $date ?? now();

        return $date->format('d-m-Y');
    }
}
