<?php

namespace SmartCms\Core\Admin\Resources\FormResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use SmartCms\Core\Admin\Resources\FormResource;

class CreateForm extends CreateRecord
{
    protected static string $resource = FormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = uniqid('form_');
        $data['style'] = 1;

        return $data;
    }
}
