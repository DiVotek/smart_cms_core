<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\MenuSection;

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

    public function getHeading(): string|Htmlable
    {
        $section = MenuSection::query()->where('parent_id', $this->record->id)->first();
        if ($section) {
            return _actions('edit').' '.$section->name;
        } else {
            return parent::getHeading();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\ViewAction::make()
                ->url(fn ($record) => $record->route())
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->openUrlInNewTab(true),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function () {
                    $url = ListStaticPages::getUrl();
                    $parent = $this->record->parent;
                    if ($parent) {
                        $menuSection = MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->first();
                        if ($menuSection) {
                            $name = $parent->parent_id ? $menuSection->name : $menuSection->name.'Categories';
                            $url = ListStaticPages::getUrl([
                                'activeTab' => $name,
                            ]);
                        }
                    }
                    $this->save(true, true);
                    $this->record->touch();

                    return redirect()->to($url);
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
