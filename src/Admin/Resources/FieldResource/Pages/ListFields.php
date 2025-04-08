<?php

namespace SmartCms\Core\Admin\Resources\FieldResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use SmartCms\Core\Admin\Base\Pages\BaseListRecords;
use SmartCms\Core\Admin\Resources\FieldResource;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Services\Schema;

class ListFields extends BaseListRecords
{
    protected static string $resource = FieldResource::class;

    protected function getResourceHeaderActions(): array
    {
        return [
            Actions\Action::make('_create')
                ->create()
                ->modalWidth(MaxWidth::Medium)
                ->form(function (Form $form) {
                    return $form->schema([
                        Schema::getName(true),
                        // Toggle::make('is_required')->default(true),
                        Select::make('type')
                            ->label(_fields('field_type'))
                            ->options([
                                'text' => 'Text',
                                'textarea' => 'Textarea',
                                'select' => 'Select',
                                'radio' => 'Radio',
                                'checkbox' => 'Checkbox',
                                'file' => 'File',
                                'date' => 'Date',
                                'email' => 'Email',
                                'number' => 'Number',
                                'tel' => 'Tel',
                                'url' => 'Url',
                            ])
                            ->default('text')
                            ->required()->native(false)->searchable(true),
                    ]);
                })->modal()->action(function (array $data) {
                    Field::query()->create([
                        'name' => $data['name'],
                        'type' => $data['type'] ?? 'text',
                        'required' => $data['is_required'] ?? false,
                        'html_id' => \Illuminate\Support\Str::slug($data['name']) . '_' . \Illuminate\Support\Str::random(5),
                    ]);
                }),
        ];
    }
}
