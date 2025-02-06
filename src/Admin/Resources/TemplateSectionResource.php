<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder;
use SmartCms\Core\Services\TableSchema;

class TemplateSectionResource extends Resource
{
    protected static ?string $model = TemplateSection::class;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('design-template');
    }

    public static function getModelLabel(): string
    {
        return _nav('model_template_section');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('model_template_section');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()->withoutGlobalScopes()->count();
    }

    public static function canDelete(Model $record): bool
    {
        return ! $record->is_system;
    }

    public static function canEdit(Model $record): bool
    {
        return ! $record->is_system;
    }

    public static function form(Form $form): Form
    {
        $components = _config()->getSections();

        return $form
            ->schema([
                Section::make('')->schema([
                    Schema::getName(true)->maxLength(255)->suffixAction(Action::make('design')
                        ->label(_fields('design'))->icon('heroicon-o-cog')
                        ->mountUsing(function ($form, $get) {
                            $form->fill(['design' => $get('design')]);
                        })
                        ->form(function ($form) use ($components) {
                            return $form->schema([
                                ViewField::make('design')
                                    ->label(_fields('design'))
                                    ->view('smart_cms::admin.design')
                                    ->viewData([
                                        'options' => $components,
                                    ])
                                    ->required()
                                    ->live()
                                    ->columnSpanFull(),
                            ]);
                        })->action(function ($data, $set, $component) {
                            $set('design', $data['design']);
                            $component->getContainer()->getComponent('dynamicTypeFields')->getChildComponentContainer()->fill();
                        })),
                    Hidden::make('design'),
                    Section::make(_fields('component_settings'))
                        ->schema(function (Get $get, $set, $record) use ($components): array {
                            $path = $get('design');
                            if (! $path) {
                                return [];
                            }
                            $currentComponent = null;
                            foreach ($components as $component) {
                                if ($component['path'] == $path) {
                                    $currentComponent = $component;
                                    break;
                                }
                            }
                            $fields = [];
                            if (! isset($currentComponent['schema'])) {
                                return [];
                            }
                            foreach ($currentComponent['schema'] as $field) {
                                $field = ArrayToField::make($field, 'value.');
                                $componentField = Builder::make($field);
                                $fields = array_merge($fields, $componentField);
                            }

                            return $fields;
                        })->live()
                        ->columnSpanFull()->key('dynamicTypeFields'),
                ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableSchema::getName(),
                TableSchema::getStatus(),
                TextColumn::make('template')->label(_nav('template')),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplateSection::route('/'),
            'create' => Pages\CreateTemplateSection::route('/create'),
            'edit' => Pages\EditTemplateSection::route('/{record}/edit'),
        ];
    }
}
