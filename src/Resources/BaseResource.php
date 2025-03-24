<?php

namespace SmartCms\Core\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use SmartCms\Core\Traits\HasHooks;

/**
 * Class BaseResource
 */
abstract class BaseResource extends JsonResource
{
    use HasHooks;

    /**
     * Applies the data hooks to the data.
     *
     * @param  array  $data  The data to apply the hooks to.
     * @return mixed The data with the hooks applied.
     */
    protected function applyDataHooks(array $data): array
    {
        return $this->applyHook('transform.data', $data, $this->resource);
    }

    /**
     * Converts the resource to an array.
     *
     * @param  Request  $request  The request to convert the resource to an array for.
     * @return array The array representation of the resource.
     */
    public function toArray($request)
    {
        return once(function () use ($request) {
            $data = $this->prepareData($request);
            $data = $this->applyDataHooks($data);

            return $data;
        });
    }

    /**
     * Gets the resource as an object.
     *
     * @return object The resource as an object.
     */
    public function get()
    {
        return (object) $this->toArray(request: request());
    }

    /**
     * Prepares the data for the resource.
     *
     * @param  Request  $request  The request to prepare the data for.
     * @return array The prepared data.
     */
    abstract protected function prepareData($request): array;

    /**
     * Validates the image.
     *
     * @param  string|null  $image  The image to validate.
     * @return string The validated image.
     */
    public function validateImage(?string $image): string
    {
        return validateImage($image ?? no_image());
    }

    /**
     * Transforms the date.
     *
     * @param  \DateTime  $date  The date to transform.
     * @return string The transformed date.
     */
    public function transformDate($date): string
    {
        $date = $date ?? now();

        return $date->format('d-m-Y');
    }
}
