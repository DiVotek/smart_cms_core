<?php

namespace SmartCms\Core\Admin\Resources\EmailResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SmartCms\Core\Admin\Resources\EmailResource;

class EditEmail extends EditRecord
{
    protected static string $resource = EmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
