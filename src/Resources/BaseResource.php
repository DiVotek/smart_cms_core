<?php

namespace SmartCms\Core\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use SmartCms\Core\Traits\HasHooks;

abstract class BaseResource extends JsonResource
{
    use HasHooks;

    // protected static $requestCache = [];

    protected function applyDataHooks(array $data)
    {
        return $this->applyHook('transform.data', $data, $this->resource);
    }

    public function toArray($request)
    {
        return once(function () use ($request) {
            $data = $this->prepareData($request);
            $data = $this->applyDataHooks($data);
            return $data;
        });
        // $cacheKey = $this->getCacheKey();
        // if (isset(static::$requestCache[$cacheKey])) {
        //     return static::$requestCache[$cacheKey];
        // }

        // $data = $this->prepareData($request);
        // $data = $this->applyDataHooks($data);

        // static::$requestCache[$cacheKey] = $data;

        // return $data;
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

    // private function getCacheKey($key = null)
    // {
    //     $prefix = static::class;
    //     return $prefix . '_' . ($key ?? $this->resource->id);
    // }
}
