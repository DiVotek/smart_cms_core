<?php

namespace SmartCms\Core\Admin\Resources\FormResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SmartCms\Core\Admin\Resources\FormResource;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
