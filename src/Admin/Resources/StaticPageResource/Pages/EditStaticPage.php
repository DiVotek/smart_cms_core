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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->icon('heroicon-o-x-circle'),
            ViewAction::make()
                ->url(fn ($record) => $record->route())
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->openUrlInNewTab(true),
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
