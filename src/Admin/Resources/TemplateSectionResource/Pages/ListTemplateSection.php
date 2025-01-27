<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;

class ListTemplateSection extends ListRecords
{
    protected static string $resource = TemplateSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make(_actions('help'))
                ->iconButton()
                ->icon('heroicon-o-question-mark-circle')
                ->modalDescription(_hints('help.page'))
                ->modalFooterActions([]),
            Actions\CreateAction::make(),
        ];
    }
}
