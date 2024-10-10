<?php

namespace SmartCms\Core\Admin\Resources\MenuSectionResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use SmartCms\Core\Admin\Resources\MenuSectionResource;

class EditMenuSectionPage extends EditRecord
{
    protected static string $resource = MenuSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
