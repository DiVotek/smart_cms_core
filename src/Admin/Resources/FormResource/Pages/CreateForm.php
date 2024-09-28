<?php

namespace SmartCms\Core\Admin\Resources\FormResource\Pages;

use SmartCms\Core\Admin\Resources\FormResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateForm extends CreateRecord
{
    protected static string $resource = FormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = uniqid('form_');
        return $data;
    }
}
