<?php

namespace SmartCms\Core\Repositories;

use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Traits\HasHooks;

abstract class BaseDto implements DtoInterface
{
    use HasHooks;

    public array $extra = [];

    abstract public function toArray(): array;

    abstract public function fromModel(Model $model): self;

    abstract public function toObject(): object;

    public function get()
    {
        return [
            ...$this->toArray(),
            ...$this->extra,
        ];
    }

    public function setExtraValue(string $key, mixed $value): void
    {
        $this->extra[$key] = $value;
    }

    public function validateImage(string $image): string
    {
        return validateImage($image);
    }

    public function transformDate($date): string
    {
        return $date->format('d-m-Y');
    }
}
