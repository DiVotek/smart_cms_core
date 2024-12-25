<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Services\Helper;

class EditTemplateSection extends EditRecord
{
    protected static string $resource = TemplateSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function ($record, $data) {
                    $this->save(true, true);
                    $this->record->touch();

                    return redirect()->to(ListTemplateSection::getUrl());
                }),
            $this->getSaveFormAction()
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->save();
                    $this->record->touch();
                })
                ->formId('form'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $schema = Helper::getComponentSchema($data['design']);
        $data['schema'] = $schema;

        return $data;
    }
}
