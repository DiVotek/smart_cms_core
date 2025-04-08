<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Core\Admin\Base\Pages\BaseEditRecord;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\MenuSection;

class EditStaticPage extends BaseEditRecord
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
                ->icon('heroicon-o-eye')
                ->openUrlInNewTab(true),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-shield-check')
                ->formId('form')
                ->action(function () {
                    $url = ListStaticPages::getUrl();
                    $parent = $this->record->parent;
                    if ($parent) {
                        $menuSection = MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->first();
                        if ($menuSection) {
                            $name = $menuSection->name;
                            if ($parent->parent_id == null && $menuSection->is_categories) {
                                $name = $menuSection->name.'Categories';
                            }
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->parent_id) {
            $parent = $this->record->parent;
            $section = MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->first();
            if ($section) {
                if ($parent->parent_id == null) {
                    if ($section->is_categories) {
                        $data['layout_id'] = $section->categories_layout_id;
                    } else {
                        $data['layout_id'] = $section->items_layout_id;
                    }
                } else {
                    $data['layout_id'] = $section->items_layout_id;
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['slug'] == null) {
            $data['slug'] = '';
        }

        return $data;
    }
}
