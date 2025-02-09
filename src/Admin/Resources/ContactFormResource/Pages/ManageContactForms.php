<?php

namespace SmartCms\Core\Admin\Resources\ContactFormResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use SmartCms\Core\Admin\Resources\ContactFormResource;

class ManageContactForms extends ManageRecords
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('help')
                ->help(_hints('help.contact_form')),
        ];
    }
}
