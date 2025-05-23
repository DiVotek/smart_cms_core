<?php

namespace SmartCms\Core\Admin\Base;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use SmartCms\Core\Traits\HasHooks;

use function Filament\Support\locale_has_pluralization;

abstract class BaseResource extends Resource
{
    use HasHooks;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static ?string $extender = null;

    protected static bool $canBulkDelete = true;

    /**
     * Define the resource label in single string
     */
    public static string $resourceLabel;

    /**
     * Define the resource group in single string
     */
    public static ?string $resourceGroup;

    abstract protected static function getFormSchema(Form $form): array;

    abstract protected static function getTableColumns(Table $table): array;

    abstract protected static function getResourcePages(): array;

    protected static function getResourceRelations(): array
    {
        return [];
    }

    protected static function getResourceSubNavigation(Page $page): array
    {
        return [];
    }

    protected static function getTableActions(Table $table): array
    {
        return [];
    }

    protected static function getTableFilters(): array
    {
        return [];
    }

    protected static function getTableBulkActions(): array
    {
        return [];
    }

    public static function form(Form $form): Form
    {
        $schema = static::getFormSchema($form);
        $schema = static::applyHook('form_schema', $schema);

        if (static::$extender) {
            $extender = app(static::$extender);
            $schema = array_merge($schema, $extender->getFormSchema());
        }

        return $form->schema($schema)->columns(1);
    }

    public static function table(Table $table): Table
    {
        $columns = static::getTableColumns($table);
        $columns = static::applyHook('table_schema', $columns);

        $actions = static::configureTableActions($table);
        $actions = static::applyHook('table_actions', $actions);

        $filters = static::configureTableFilters($table);
        $filters = static::applyHook('table_filters', $filters);

        $bulkActions = static::configureTableBulkActions($table);
        $bulkActions = static::applyHook('table_bulk_actions', $bulkActions);

        if (static::$extender) {
            $extender = app(static::$extender);
            $columns = array_merge($columns, $extender->getTableColumns());
            $filters = array_merge($filters, $extender->getTableFilters());
        }

        $table = $table->columns($columns)->actions($actions)->filters($filters)->bulkActions($bulkActions);

        return $table;
    }

    public static function getPages(): array
    {
        $pages = static::getResourcePages();
        $pages = static::applyHook('pages', $pages);

        if (static::$extender) {
            $extender = app(static::$extender);
            $pages = array_merge($pages, $extender->getPages());
        }

        return $pages;
    }

    protected static function configureTableActions(Table $table): array
    {
        $actions = static::getTableActions($table);
        $actions = static::applyHook('table_actions', $actions);

        return [
            \Filament\Tables\Actions\EditAction::make()->modalWidth(MaxWidth::TwoExtraLarge),
            \Filament\Tables\Actions\DeleteAction::make(),
            ...$actions,
        ];
    }

    protected static function configureTableFilters(Table $table): array
    {
        $filters = static::getTableFilters();
        $filters = static::applyHook('table_filters', $filters);

        return $filters;
    }

    protected static function configureTableBulkActions(Table $table): array
    {
        $bulkActions = static::getTableBulkActions();
        $bulkActions = static::applyHook('table_bulk_actions', $bulkActions);

        return [
            \Filament\Tables\Actions\BulkActionGroup::make([
                ...(static::$canBulkDelete ? [\Filament\Tables\Actions\DeleteBulkAction::make()] : []),
                ...$bulkActions,
            ]),
        ];
    }

    public static function getRelations(): array
    {
        $relations = static::getResourceRelations();
        $relations = static::applyHook('relations', $relations);

        return $relations;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()->withoutGlobalScopes()->count();
    }

    public static function getModelLabel(): string
    {
        return _nav(static::$resourceLabel);
    }

    public static function getPluralModelLabel(): string
    {
        $label = static::$resourceLabel;
        if (locale_has_pluralization()) {
            $label = Str::plural(static::$resourceLabel);
        }
        $label = strtolower($label);
        $label = str_replace(' ', '_', $label);

        return _nav($label);
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$resourceGroup ? _nav(static::$resourceGroup) : null;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $subNavigation = static::getResourceSubNavigation($page);
        $subNavigation = static::applyHook('sub_navigation', $subNavigation);

        if (static::$extender) {
            $extender = app(static::$extender);
            $subNavigation = array_merge($subNavigation, $extender->getSubNavigation());
        }

        return $page->generateNavigationItems($subNavigation);
    }
}
