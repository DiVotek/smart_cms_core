<?php

namespace SmartCms\Core\Repositories\Field;

use SmartCms\Core\Traits\Dto\AsDto;

class FieldDto
{
    use AsDto;

    public function __construct(
        public int|string $id,
        public string $name,
        public string $type,
        public string $html_id,
        public string $mask,
        public string $class,
        public string $style,
        public string $label,
        public string $description,
        public string $placeholder,
        public array $options,
        public bool $required,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'html_id' => $this->html_id,
            'mask' => $this->mask,
            'class' => $this->class,
            'style' => $this->style,
            'label' => $this->label,
            'description' => $this->description,
            'placeholder' => $this->placeholder,
            'options' => $this->options,
            'required' => $this->required ?? false,
        ];
    }
}
