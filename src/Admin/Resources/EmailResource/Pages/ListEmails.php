<?php

namespace SmartCms\Core\Admin\Resources\EmailResource\Pages;


use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\EmailResource;

class ListEmails extends ListRecords
{
    protected static string $resource = EmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
