<?php

namespace SmartCms\Core\Repositories;

abstract class BaseDto implements DtoInterface
{
    abstract public function toArray(): array;
}
