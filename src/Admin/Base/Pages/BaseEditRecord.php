<?php

namespace SmartCms\Core\Admin\Base\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SmartCms\Core\Traits\HasHooks;

abstract class BaseEditRecord extends EditRecord
{
    use HasHooks;

    public function getBreadcrumb(): string
    {
        if (isset($this->record->name)) {
            return $this->record->name;
        }

        return '';
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
            Actions\DeleteAction::make(),
            ...$actions,
            Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function () {
                    $this->save(true, true);
                    $this->record->touch();

                    return redirect()->to(static::$resource::getUrl('index'));
                }),
            $this->getSaveFormAction()
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->save();
                    $this->record->touch();
                })
                ->formId('form'),
        ];
    }
}
