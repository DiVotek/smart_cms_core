<?php

namespace SmartCms\Core\Admin\Resources\AdminResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\AdminResource;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    public function getBreadcrumbs(): array
    {
        if (config('shared.admin.breadcrumbs', false)) {
            return parent::getBreadcrumbs();
        }

        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('help')
                ->help(_hints('help.admin')),
            Actions\CreateAction::make()->create(),
        ];
    }
}
