<?php

namespace SmartCms\Core\Admin\Resources\FormResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use SmartCms\Core\Admin\Base\Pages\BaseListRecords;
use SmartCms\Core\Admin\Resources\FormResource;
use SmartCms\Core\Models\Form as ModelsForm;
use SmartCms\Core\Services\Schema;

class ListForms extends BaseListRecords
{
    protected static string $resource = FormResource::class;

    protected function getResourceHeaderActions(): array
    {
        return [
            Actions\Action::make('_create')
                ->create()
                ->modalWidth(MaxWidth::Medium)
                ->form(function (Form $form) {
                    return $form->schema([
                        Schema::getName(true),
                    ]);
                })->action(function (array $data) {
                    ModelsForm::query()->create([
                        'name' => $data['name'],
                        'code' => uniqid('form_'),
                        'fields' => [],
                    ]);
                }),
        ];
    }
}
