<?php

namespace SmartCms\Core\Admin\Resources\FormResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\FormResource;
use SmartCms\Core\Models\Form as ModelsForm;
use SmartCms\Core\Services\Schema;

class ListForms extends ListRecords
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label(_actions('create'))
                ->form(function (Form $form) {
                    return $form->schema([
                        Schema::getName(true),
                    ]);
                })->action(function (array $data) {
                    ModelsForm::query()->create([
                        'name' => $data['name'],
                        'code' => uniqid('form_'),
                        'style' => '',
                        'fields' => [],
                    ]);
                }),
        ];
    }
}
