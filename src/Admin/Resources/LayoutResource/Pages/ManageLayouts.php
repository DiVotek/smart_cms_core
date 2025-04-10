<?php

namespace SmartCms\Core\Admin\Resources\LayoutResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use SmartCms\Core\Admin\Base\Pages\BaseManageRecords;
use SmartCms\Core\Admin\Resources\LayoutResource;
use SmartCms\Core\Models\Layout;

class ManageLayouts extends BaseManageRecords
{
    protected static string $resource = LayoutResource::class;

    protected function getResourceHeaderActions(): array
    {
        return [
            Action::make('settings')->settings()
                ->fillForm(function (): array {
                    return [
                        'header' => _settings('layouts.header', []),
                        'footer' => _settings('layouts.footer', []),
                    ];
                })
                ->action(function (array $data): void {
                    setting([
                        sconfig('layouts.header') => $data['header'],
                        sconfig('layouts.footer') => $data['footer'],
                    ]);
                })
                ->form(function ($form) {
                    return $form
                        ->schema([
                            Select::make('header')->options(Layout::query()->where('path', 'like', 'header%')->pluck('name', 'id'))->searchable(),
                            Select::make('footer')->options(Layout::query()->where('path', 'like', 'footer%')->pluck('name', 'id'))->searchable(),
                        ]);
                }),
        ];
    }
}
