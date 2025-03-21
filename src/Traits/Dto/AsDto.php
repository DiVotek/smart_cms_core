<?php

namespace SmartCms\Core\Traits\Dto;

use DateTime;

trait AsDto
{
    public array $extra = [];

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toObject(): object
    {
        return (object) $this->toArray();
    }

    public function transformDate(DateTime $date): string
    {
        return $date->format('d-m-Y');
    }

    public static function factory(...$args): self
    {
        return new self(...$args);
    }

    public function setExtraValue(string $key, mixed $value): void
    {
        $this->extra[$key] = $value;
    }

    public function get()
    {
        return $this->toObject();
    }

    public function validateImage(string $image): string
    {
        return validateImage($image ?? no_image());
    }
}
