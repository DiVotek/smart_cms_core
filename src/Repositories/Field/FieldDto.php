<?php

namespace SmartCms\Core\Repositories\Field;

class FieldDto
{
    public function __construct(
        public int $id,
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
            'label' => $this->label[current_lang()] ?? $this->label[main_lang()] ?? '',
            'description' => $this->description[current_lang()] ?? $this->description[main_lang()] ?? '',
            'placeholder' => $this->placeholder[current_lang()] ?? $this->placeholder[main_lang()] ?? '',
            'options' => $this->options ?? [],
            'required' => $this->required ?? false,
        ];
    }
}
