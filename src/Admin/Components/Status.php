<?php

namespace SmartCms\Core\Admin\Components;

use Filament\Forms\Components\Toggle;
use SmartCms\Core\Services\Status as ServicesStatus;

class Status
{
    public static function make(string $column = 'status', $isRequired = true): Toggle
    {
        return Toggle::make($column)->label(_fields('status'))->required($isRequired)
            ->default(ServicesStatus::ON)
            ->helperText(__('If status is off, the record will not be displayed on the site'));
    }
}
