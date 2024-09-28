<?php

namespace SmartCms\Core\Admin\Resources\AdminResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use SmartCms\Core\Admin\Resources\AdminResource;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['permissions'] = [];
        $data['notifications'] = [];

        return $data;
    }
}
