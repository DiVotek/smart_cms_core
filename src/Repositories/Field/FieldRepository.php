<?php

namespace SmartCms\Core\Repositories\Field;

use SmartCms\Core\Models\Field;
use SmartCms\Core\Repositories\RepositoryInterface;
use SmartCms\Core\Resources\FieldResource;
use SmartCms\Core\Traits\Repository\AsRepository;

class FieldRepository implements RepositoryInterface
{
    use AsRepository;

    public function findMultiple(array $ids): array
    {
        $fields = Field::query()->whereIn('id', $ids)->get();

        return FieldResource::collection($fields)->get();
    }

    public function find(int $id, bool $required = true): object
    {
        $field = Field::query()->find($id);
        $field->required = $required;

        return FieldResource::make($field)->get();
    }
}
