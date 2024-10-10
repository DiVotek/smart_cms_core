<?php

namespace SmartCms\Core\Admin\Resources\MenuSectionResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\MenuSectionResource;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Services\Schema;

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
