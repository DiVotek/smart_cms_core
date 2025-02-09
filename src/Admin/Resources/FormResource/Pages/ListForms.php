<?php

namespace SmartCms\Core\Admin\Resources\FormResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;
use SmartCms\Core\Admin\Resources\FormResource;
use SmartCms\Core\Models\Form as ModelsForm;
use SmartCms\Core\Services\Schema;

class ListForms extends ListRecords
{
    protected static string $resource = FormResource::class;

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
                ->help(_hints('help.forms')),
            Actions\Action::make('create')
                ->create()
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
