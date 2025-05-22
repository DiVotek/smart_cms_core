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
    private static array $formFields = [];

    /** @var Closure[] */
    private static array $tableColumns = [];

    /** @var Closure[] */
    private static array $filters = [];

    /** @var array<string, string> */
    private static array $pages = [];

    /** @var Closure[] */
    private static array $actions = [];

    public function addField(Closure|Field $fieldCallback): self
    {
        self::$formFields[] = $fieldCallback;

        return $this;
    }

    public function addColumn(Closure|Column $columnCallback): self
    {
        self::$tableColumns[] = $columnCallback;

        return $this;
    }

    public function addFilter(Closure|BaseFilter $filterCallback): self
    {
        self::$filters[] = $filterCallback;

        return $this;
    }

    public function addPage(string $slug, string $pageClass): self
    {
        self::$pages[$slug] = $pageClass;

        return $this;
    }

    public function addAction(Closure|Action $actionCallback): self
    {
        self::$actions[] = $actionCallback;

        return $this;
    }

    final public function getFormSchema(): array
    {
        $schema = [];
        foreach (self::$formFields as $callback) {
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
        foreach (self::$tableColumns as $callback) {
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
        foreach (self::$filters as $callback) {
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
        foreach (self::$pages as $slug => $class) {
            $pages[$slug] = $class::route('/{record}/'.$slug);
        }

        return $pages;
    }

    final public function getSubNavigation(): array
    {
        return array_values(self::$pages);
    }

    final public function getActions(): array
    {
        $actions = [];
        foreach (self::$actions as $callback) {
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
