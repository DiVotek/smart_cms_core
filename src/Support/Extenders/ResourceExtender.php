<?php

declare(strict_types=1);

namespace SmartCms\Core\Support\Extenders;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Field;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;

abstract class ResourceExtender
{
    /** @var Closure[] */
    public static array $formFields = [];

    /** @var Closure[] */
    public static array $tableColumns = [];

    /** @var Closure[] */
    public static array $filters = [];

    /** @var array<string, string> */
    public static array $pages = [];

    /** @var Closure[] */
    public static array $actions = [];

    public function addField(Closure|Field $fieldCallback): self
    {
        static::$formFields[] = $fieldCallback;
        return $this;
    }

    public function addColumn(Closure|Column $columnCallback): self
    {
        static::$tableColumns[] = $columnCallback;

        return $this;
    }

    public function addFilter(Closure|BaseFilter $filterCallback): self
    {
        static::$filters[] = $filterCallback;

        return $this;
    }

    public function addPage(string $slug, string $pageClass): self
    {
        static::$pages[$slug] = $pageClass;
        return $this;
    }

    public function addAction(Closure|Action $actionCallback): self
    {
        static::$actions[] = $actionCallback;

        return $this;
    }

    final public function getFormSchema(): array
    {
        $schema = [];
        foreach (static::$formFields as $callback) {
            if ($callback instanceof Closure) {
                $schema = array_merge($schema, $callback());
            } else {
                $schema[] = $callback;
            }
        }

        return $schema;
    }

    final public function getTableColumns(): array
    {
        $columns = [];
        foreach (static::$tableColumns as $callback) {
            if ($callback instanceof Closure) {
                $columns = array_merge($columns, $callback());
            } else {
                $columns[] = $callback;
            }
        }

        return $columns;
    }

    final public function getTableFilters(): array
    {
        $filters = [];
        foreach (static::$filters as $callback) {
            if ($callback instanceof Closure) {
                $filters = array_merge($filters, $callback());
            } else {
                $filters[] = $callback;
            }
        }

        return $filters;
    }

    final public function getPages(): array
    {
        $pages = [];
        foreach (static::$pages as $slug => $class) {
            $pages[$slug] = $class::route('/{record}/' . $slug);
        }

        return $pages;
    }

    final public function getSubNavigation(): array
    {
        return array_values(static::$pages);
    }

    final public function getActions(): array
    {
        $actions = [];
        foreach (static::$actions as $callback) {
            if ($callback instanceof Closure) {
                $action = $callback();
                if ($action instanceof Action || $action instanceof ActionGroup) {
                    $actions[] = $action;
                }
                if (is_array($action)) {
                    $actions = array_merge($actions, $action);
                }
            } else {
                $actions[] = $callback;
            }
        }

        return $actions;
    }
}
