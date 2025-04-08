<?php

namespace SmartCms\Core\Admin\Base\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\MaxWidth;
use SmartCms\Core\Traits\HasHooks;

abstract class BaseManageRecords extends ManageRecords
{
    use HasHooks;

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
            Actions\Action::make('help')->help(_hints('help.'.$shortClassName))->modalFooterActions([]),
            ...$actions,
            Actions\CreateAction::make()->modalWidth(MaxWidth::Large),
        ];
    }
}
