<?php

namespace SmartCms\Core\Admin\Base\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Traits\HasHooks;

abstract class BaseListRecords extends ListRecords
{
    use HasHooks;

    /**
     * Define if the resource should show the create button
     */
    public static ?bool $showCreate = true;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getResourceHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        $shortClassName = (new \ReflectionClass($this))->getShortName();
        $actions = $this->getResourceHeaderActions();
        $actions = $this->applyHook('header_actions', $actions);
        return [
            Actions\Action::make('help')->help(_hints('help.' . $shortClassName))->modalFooterActions([]),
            ...$actions,
            Actions\CreateAction::make()->visible(static::$showCreate),
        ];
    }
}
