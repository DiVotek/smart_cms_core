<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\LayoutResource\Pages;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Services\Frontend\LayoutService;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder;
use SmartCms\Core\Services\TableSchema;

class LayoutResource extends BaseResource
{
    protected static ?string $model = Layout::class;

    protected static ?int $navigationSort = 3;

    public static string $resourceLabel = 'layout';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('design-template');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getFormSchema(Form $form): array
    {
        $instance = $form->getModelInstance();
        $schema = LayoutService::make()->getSectionMetadata($instance->path ?? '');
        $schema = $schema['schema'] ?? [];
        $schemaFields = [];
        foreach ($schema as $value) {
            $field = ArrayToField::make($value, 'value.');
            $componentField = Builder::make($field);
            $schemaFields = array_merge($schemaFields, $componentField);
        }

        return $schemaFields;
    }

    public static function getTableColumns(Table $table): array
    {
        return [
            TableSchema::getName(),
            TableSchema::getStatus()->disabled(),
            TableSchema::getUpdatedAt(),
        ];
    }

    public static function getResourcePages(): array
    {
        return [
            'index' => Pages\ManageLayouts::route('/'),
        ];
    }
}
