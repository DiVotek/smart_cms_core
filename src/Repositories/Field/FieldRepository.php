<?php

namespace SmartCms\Core\Repositories\Field;

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

    public function find(int $id): object
    {
        $field = Field::query()->find($id);

        return $this->transform($field);
    }

    public function transform(Field $field): object
    {
        $mask = $field->mask ?? [];
        $mask = $mask[current_lang()] ?? $mask[main_lang()] ?? '';

        return new FieldDto(
            $field->id,
            $field->name,
            $field->type,
            $field->html_id,
            $mask,
            $field->class ?? '',
            $field->style ?? '',
            $field->label[current_lang()] ?? $field->label[main_lang()] ?? '',
            $field->description[current_lang()] ?? $field->description[main_lang()] ?? '',
            $field->placeholder[current_lang()] ?? $field->placeholder[main_lang()] ?? '',
            $field->options ?? [],
            $field->required ?? false,
        );
    }
}
