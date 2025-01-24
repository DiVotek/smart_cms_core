<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\LayoutResource\Pages as Pages;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder;
use SmartCms\Core\Services\TableSchema;

class LayoutResource extends Resource
{
    protected static ?string $model = Layout::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::withoutGlobalScopes()->count();
    }

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
        return _nav('layout');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('layouts');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        $instance = $form->getModelInstance();
        $schema = [];
        if ($instance) {
            if (config('app.env') == 'production') {
                $layoutSchema = $instance->schema ?? [];
            } else {
                $layouts = collect(_config()->getLayouts());
                $layout_schema = $layouts->where('path', $instance->path)->first();
                if ($layout_schema && is_array($layout_schema['schema'])) {
                    $layoutSchema = $layout_schema['schema'];
                }
            }
            foreach ($layoutSchema as $value) {
                $field = ArrayToField::make($value, 'value.');
                $componentField = Builder::make($field);
                $schema = array_merge($schema, $componentField);
            }
        }
        if (count($schema) > 0) {
            $schema = [
                Section::make('')->schema($schema),
            ];
        }

        return $form->schema($schema);
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayouts::route('/'),
            'create' => Pages\CreateLayout::route('/create'),
            'edit' => Pages\EditLayout::route('/{record}/edit'),
        ];
    }
}
