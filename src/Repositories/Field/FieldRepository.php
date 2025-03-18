<?php

namespace SmartCms\Core\Repositories\Field;

use Ramsey\Uuid\Uuid;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Repositories\RepositoryInterface;
use SmartCms\Core\Traits\Repository\AsRepository;

class FieldRepository implements RepositoryInterface
{
    use AsRepository;

    public function findMultiple(array $ids): array
    {
        $fields = Field::query()->whereIn('id', $ids)->get();

        return $fields->map(fn (Field $field) => $this->transform($field))->toObject();
    }

    public function find(int $id, bool $required = true): object
    {
        $field = Field::query()->find($id);
        $field->required = $required;

        return $this->transform($field);
    }

    public function transform(Field $field): object
    {
        $label = $field->data[current_lang()]['label'] ?? $field->data['label'] ?? '';
        $description = $field->data[current_lang()]['description'] ?? $field->data['description'] ?? '';
        $placeholder = $field->data[current_lang()]['placeholder'] ?? $field->data['placeholder'] ?? '';

        return new FieldDto(
            id: $field->id,
            name: $field->name(),
            type: $field->type,
            html_id: Uuid::uuid4(),
            mask: $field->data['mask'] ?? '',
            class: $field->class ?? '',
            style: $field->style ?? '',
            label: $label,
            description: $description,
            placeholder: $placeholder,
            options: $field->options ?? [],
            required: $field->required ?? false,
        );
    }
}
