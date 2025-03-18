<?php

namespace SmartCms\Core\Admin\Base\Pages;

use Filament\Resources\Pages\CreateRecord;
use SmartCms\Core\Traits\HasHooks;
use Filament\Actions;

abstract class BaseCreateRecord extends CreateRecord
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
        $actions = $this->getResourceHeaderActions();
        $actions = $this->applyHook('header_actions', $actions);
        return [
            ...$actions,
            Actions\Action::make(_actions('save_close'))
                ->label('Create & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function () {
                    $this->create(false);

                    return redirect()->to(static::$resource::getUrl('index'));
                }),
            $this->getCreateFormAction()
                ->label(_actions('create'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->create(false);
                })
                ->formId('form')
        ];
    }
}
