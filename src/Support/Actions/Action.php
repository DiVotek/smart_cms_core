<?php

declare(strict_types=1);

namespace SmartCms\Core\Support\Actions;

use Livewire\Component;

abstract class Action
{
    public function __construct(public string $name = '', public array $params = [], public ?Component $instance = null) {}

    abstract public function handle(): mixed;
}
