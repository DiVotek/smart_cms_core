<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\MenuSectionResource\Pages;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class MenuSectionResource extends Resource
{
    protected static ?string $model = MenuSection::class;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

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
        $customFields = _config()->getCustomFields();
        $customFields = array_map(function ($field) {
            return $field['name'] ?? '';
        }, $customFields);
        $customFields = array_filter($customFields);

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Schema::getName(),
                        Schema::getSorting(),
                        IconPicker::make('icon')->columns(6)
                            ->cacheable(false)
                            ->label(_fields('icon')),
                        Toggle::make('is_categories')
                            ->label(_fields('is_categories'))
                            ->live()
                            ->default(false),
                        Schema::getSelect('custom_fields')->label(_fields('custom_fields'))->options($customFields)->default([]),
                        Schema::getTemplateBuilder('template'),
                        Schema::getTemplateBuilder('categories_template')->label(_fields('categories_template'))->hidden(function ($get) {
                            return ! $get('is_categories');
                        }),

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
                Tables\Actions\DeleteAction::make()->action(function ($record) {
                    $pageParent = Page::query()->where('id', $record->parent_id)->first();
                    if ($pageParent) {
                        Page::query()->where('parent_id', $pageParent->id)->update([
                            'parent_id' => null,
                            'status' => 0,
                        ]);
                        $pageParent->delete();
                    }
                    $record->delete();
                }),
            ])
            ->reorderable('sorting')
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
