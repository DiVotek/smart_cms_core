<?php

namespace SmartCms\Core\Resources;

use Ramsey\Uuid\Uuid;

class FieldResource extends BaseResource
{
    public function prepareData($request): array
    {
        $description = $this->resource->data[current_lang()]['description'] ?? $this->resource->data['description'] ?? '';
        $placeholder = $this->resource->data[current_lang()]['placeholder'] ?? $this->resource->data['placeholder'] ?? '';

        return [
            'name' => $this->resource->name(),
            'html_name' => $this->resource->html_id,
            'type' => $this->resource->type,
            'html_id' => Uuid::uuid4(),
            'mask' => $this->resource->data['mask'] ?? '',
            'class' => $this->resource->class,
            'style' => $this->resource->style,
            'label' => $this->resource->name(),
            'description' => $description,
            'placeholder' => $placeholder,
            'options' => $this->resource->options ?? [],
            'required' => $this->resource->required ?? false,
        ];
    }
}
