<?php

namespace SmartCms\Core\Admin\Resources\LayoutResource\Pages;

use SmartCms\Core\Admin\Base\Pages\BaseManageRecords;
use SmartCms\Core\Admin\Resources\LayoutResource;

class ManageLayouts extends BaseManageRecords
{
    protected static string $resource = LayoutResource::class;

    protected function mutateFormDataBeforeSave(array $data): array {}
}
