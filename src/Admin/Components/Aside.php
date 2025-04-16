<?php

namespace SmartCms\Core\Admin\Components;

use Filament\Forms\Components\Section;

class Aside
{
    public static function make($isStatus = true): Section
    {
        $fields = [
            CreatedAt::make(),
            UpdatedAt::make(),
        ];
        if ($isStatus) {
            $fields[] = Status::make();
        }

        return Section::make()->schema($fields);
    }
}
