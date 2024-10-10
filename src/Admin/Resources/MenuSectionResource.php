<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use SmartCms\Core\Admin\Resources\MenuSectionResource\Pages as Pages;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class MenuSectionResource extends Resource
{
    protected static ?string $model = MenuSection::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('pages');
    }

    public static function getModelLabel(): string
    {
        return _nav('model_menu_section');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('model_menu_section');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Schema::getName(),
                        Schema::getSorting(),
                        IconPicker::make('icon')->columns(6)
                            ->label(_fields('icon')),
                        Toggle::make('is_categories')
                            ->label(_fields('is_categories'))
                            ->default(false),
                        Schema::getRepeater('custom_fields')->schema([])->default([]),
                        // Select::make('parent_id')
                        //     ->label(_fields('parent'))
                        //     ->options(function (): array {
                        //         return Page::query()->pluck('name', 'id')->toArray();
                        //     })
                        //     ->required(),
                        Schema::getTemplateBuilder('template'),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableSchema::getName(),
                TableSchema::getSorting(),
                TableSchema::getViews(),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([
                TableSchema::getFilterStatus(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\EditAction::make('Page')
                    ->label(_actions('edit_page'))
                    ->url(function ($record) {
                        return StaticPageResource::getUrl('edit', ['record' => $record->parent_id]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->reorderable('sorting')
            ->headerActions([
                Schema::helpAction('Static page help text')->hidden(function () {
                    return (bool) request('parent');
                }),
                Tables\Actions\Action::make('Template')
                    ->label(_actions('template'))
                    ->slideOver()
                    ->icon('heroicon-o-cog')
                    ->fillForm(function (): array {
                        return [
                            'template' => _settings('static_page_template', []),
                        ];
                    })
                    ->action(function (array $data): void {
                        setting([
                            sconfig('static_page_template') => $data['template'],
                        ]);
                    })
                    ->hidden(function () {
                        return (bool) request('parent');
                    })
                    ->form(function ($form) {
                        return $form
                            ->schema([
                                Section::make('')->schema([
                                    Schema::getTemplateBuilder()->label(_fields('template')),
                                ]),
                            ]);
                    }),
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
            'index' => Pages\ListMenuSections::route('/'),
            'create' => Pages\CreateMenuSectionPage::route('/create'),
            'edit' => Pages\EditMenuSectionPage::route('/{record}/edit'),
        ];
    }
}
