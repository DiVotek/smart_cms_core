<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\LayoutResource\Pages;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Services\Config;
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

        return $schema;
    }

    public static function getTableColumns(Table $table): array
    {
        return [
            TableSchema::getName(),
            TableSchema::getStatus()->disabled(),
            TextColumn::make('template')->label(_nav('template')),
            TableSchema::getUpdatedAt(),
        ];
    }

    public static function getTableActions(Table $table): array
    {
        return [
            Action::make('update_schema')->iconButton()
                ->tooltip(_actions('update_schema'))
                ->label(_actions('update_schema'))->icon('heroicon-o-arrow-path')->action(function ($record) {
                    $config = new Config;
                    $config->initLayout($record->path);
                    Notification::make()->title(_actions('success'))->success()->send();
                }),
        ];
    }

    public static function getResourcePages(): array
    {
        return [
            'index' => Pages\ManageLayouts::route('/'),
        ];
    }
}
