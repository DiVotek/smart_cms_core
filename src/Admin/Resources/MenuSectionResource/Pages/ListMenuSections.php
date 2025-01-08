<?php

namespace SmartCms\Core\Admin\Resources\MenuSectionResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\MenuSectionResource;
use SmartCms\Core\Services\Schema;

class ListMenuSections extends ListRecords
{
    protected static string $resource = MenuSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make(_actions('help'))
                ->iconButton()
                ->icon('heroicon-o-question-mark-circle')
                ->modalDescription(_hints('help.menu_sections'))
                ->modalFooterActions([]),
            // Actions\Action::make('Template')
            //     ->label(_actions('template'))
            //     ->slideOver()
            //     ->icon('heroicon-o-cog')
            //     ->fillForm(function (): array {
            //         return [
            //             'template' => _settings('static_page_template', []),
            //         ];
            //     })
            //     ->action(function (array $data): void {
            //         setting([
            //             sconfig('static_page_template') => $data['template'],
            //         ]);
            //     })
            //     ->hidden(function () {
            //         return (bool) request('parent');
            //     })
            //     ->form(function ($form) {
            //         return $form
            //             ->schema([
            //                 Section::make('')->schema([
            //                     Schema::getTemplateBuilder()->label(_fields('template')),
            //                 ]),
            //             ]);
            //     }),
            Actions\CreateAction::make(),
        ];
    }
}
