<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;

class EditMenuSection extends EditRecord
{
    protected static string $resource = StaticPageResource::class;

    public static function getNavigationLabel(): string
    {
        return _nav('menu_section_settings');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-cog';
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('')->schema([
                Schema::getName(),
                Schema::getSorting(),
                // IconPicker::make('icon')
                //     ->label(_fields('icon'))
                //     ->sets(['heroicons'])
                //     ->placeholder('heroicon-o-menu')->preload()->searchable(false),
                Select::make('categories_layout_id')
                    ->label(_fields('categories_layout'))
                    ->disabled()->options(Layout::query()->pluck('name', 'id')->toArray()),
                Select::make('items_layout_id')
                    ->label(_fields('items_layout'))
                    ->disabled()->options(Layout::query()->pluck('name', 'id')->toArray()),
                Toggle::make('is_categories')->label(_fields('is_categories'))->disabled(),
                Schema::getTemplateBuilder('categories_template')->label(_fields('categories_template'))->hidden(function ($get) {
                    return ! $get('is_categories');
                }),
                Schema::getTemplateBuilder('items_template')->label(_fields('items_template')),
            ]),
        ])->columns(1);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $section = MenuSection::query()->where('parent_id', $this->record->id)->first();
        $data = $section->toArray();

        return $data;
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
        $section = MenuSection::query()->where('parent_id', $this->record->id)->first();

        return [
            // \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\Action::make('transfer')->label(_actions('transfer'))->icon('heroicon-o-arrows-right-left')
                ->color('danger')
                ->form(function ($form) use ($section) {
                    return $form->schema([
                        Select::make('parent_id')
                            ->label(_fields('menu_section'))
                            ->options(MenuSection::query()->where('parent_id', '!=', $this->record->id)->where('is_categories', $section->is_categories)->pluck('name', 'id')->toArray())
                            ->required(),
                    ]);
                })->action(function ($data) use ($section) {
                    $newSection = MenuSection::query()->where('id', $data['parent_id'])->first();
                    Page::query()->where('parent_id', $section->parent_id)->update([
                        'parent_id' => $newSection->parent_id,
                    ]);
                    Notification::make()->title(_actions('success'))->success()->send();
                }),
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $section = MenuSection::query()->where('parent_id', $record->id)->first();
        $section->update($data);

        return $record;
    }
}
