<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Base\Pages\BaseListRecords;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;

class ListTemplateSection extends BaseListRecords
{
    protected static string $resource = TemplateSectionResource::class;

    public function getBreadcrumbs(): array
    {
        if (config('shared.admin.breadcrumbs', false)) {
            return parent::getBreadcrumbs();
        }

        return [];
    }

    protected function getResourceHeaderActions(): array
    {
        return [];
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\Action::make('help')
    //             ->help(_hints('help.page')),
    //         Actions\CreateAction::make()->create(),
    //     ];
    // }
}
