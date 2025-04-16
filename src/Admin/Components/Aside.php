<?php

namespace SmartCms\Core\Admin\Components;

use Filament\Forms\Components\Section;

class Aside
{
    public static function make($isStatus = true, array $additional = []): Section
    {
        $fields = [
            CreatedAt::make(),
            UpdatedAt::make(),
        ];
        if ($isStatus) {
            $fields[] = Status::make();
        }
        $fields = array_merge($fields, $additional);
        return Section::make()->schema($fields);
    }
}
