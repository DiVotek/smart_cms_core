<?php

namespace SmartCms\Core\Admin\Resources\SeoResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SmartCms\Core\Admin\Resources\SeoResource;

class EditSeo extends EditRecord
{
    protected static string $resource = SeoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
