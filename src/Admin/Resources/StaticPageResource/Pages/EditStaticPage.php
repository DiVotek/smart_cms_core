<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Core\Admin\Resources\StaticPageResource;

class EditStaticPage extends EditRecord
{
    protected static string $resource = StaticPageResource::class;

    public static function getNavigationLabel(): string
    {
        return _nav('general');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-cog';
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }


    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\ViewAction::make()
                ->url(fn($record) => $record->route())
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->openUrlInNewTab(true),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function () {
                    $this->save(true, true);
                    $this->record->touch();

                    return redirect()->to(ListStaticPages::getUrl());
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['slug'] == null) {
            $data['slug'] = '';
        }

        return $data;
    }
}
