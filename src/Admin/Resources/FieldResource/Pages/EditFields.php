<?php

namespace SmartCms\Core\Admin\Resources\FieldResource\Pages;

use SmartCms\Core\Admin\Base\Pages\BaseEditRecord;
use SmartCms\Core\Admin\Resources\FieldResource;

class EditFields extends BaseEditRecord
{
    protected static string $resource = FieldResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make(),
    //     ];
    // }
}
