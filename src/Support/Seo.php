<?php

declare(strict_types=1);

namespace SmartCms\Core\Support;

class Seo
{
    public ?string $title = null;
    public ?string $description = null;
    public ?string $keywords = null;
    public ?string $image = null;
    public array $microdata = [];

    public function title(?string $value = null): string|static
    {
        if ($value === null) return $this->title ?? config('app.name');
        $this->title = $value;
        return $this;
    }

    public function description(?string $value = null): string|static
    {
        if ($value === null) return $this->description ?? '';
        $this->description = $value;
        return $this;
    }

    public function keywords(?string $value = null): string|static
    {
        if ($value === null) return $this->keywords ?? '';
        $this->keywords = $value;
        return $this;
    }

    public function image(?string $value = null): string|static
    {
        if ($value === null) return $this->image ?? '';
        $this->image = $value;
        return $this;
    }

    public function addMicrodata(string $type, array $data): static
    {
        $this->microdata[$type] = $data;
        return $this;
    }

    public function getMicrodata(): array
    {
        return $this->microdata;
    }
}
