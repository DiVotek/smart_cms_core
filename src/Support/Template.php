<?php

declare(strict_types=1);

namespace SmartCms\Core\Support;

class Template
{
    public array $template = [];

    public function template(?array $value = null): array|static
    {
        if ($value === null) return $this->template ?? [];
        $this->template = $value;
        return $this;
    }
}
