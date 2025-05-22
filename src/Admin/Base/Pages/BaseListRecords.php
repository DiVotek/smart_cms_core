<?php

namespace SmartCms\Core\Admin\Base\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
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
        if (app(static::$resource)::$extender) {
            $extender = app(app(static::$resource)::$extender);
            $actions = array_merge($actions, $extender->getActions());
        }
        return [
            Actions\Action::make('help')
                ->modalWidth(MaxWidth::TwoExtraLarge)
                ->help(_hints('help.' . $shortClassName))
                ->modalFooterActions([]),
            ...$actions,
            Actions\CreateAction::make()->visible(static::$showCreate),
        ];
    }
}
