<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Base\Pages\BaseEditRecord;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;

class EditMenuSection extends BaseEditRecord
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

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('')->schema([
                Schema::getName(),
                Schema::getSorting(),
            ])->columns(2),
            Section::make('')->schema([
                Select::make('items_layout_id')
                    ->label(_fields('items_layout'))
                    ->options(Layout::query()->where('path', 'like', '%groups.items.%')->pluck('name', 'id')->toArray()),
                Schema::getTemplateBuilder('template')->label(_fields('items_template')),
            ]),
            Section::make('')->schema([
                Select::make('categories_layout_id')
                    ->label(_fields('categories_layout'))
                    ->options(Layout::query()->where('path', 'like', '%groups.categories.%')->pluck('name', 'id')->toArray()),
                Schema::getTemplateBuilder('categories_template')->label(_fields('categories_template')),
            ])->hidden(fn ($get) => ! $get('is_categories')),
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

    protected function getResourceHeaderActions(): array
    {
        $section = MenuSection::query()->where('parent_id', $this->record->id)->first();

        return [
            \Filament\Actions\Action::make('delete_menu_section')->label(_actions('delete'))->icon('heroicon-o-trash')
                ->color('danger')
                // ->disabled(function ($record) {
                //     $section = MenuSection::query()->where('parent_id', $this->record->id)->first();
                //     return Page::query()->where('parent_id', $section->parent_id)->exists();
                // })
                ->requiresConfirmation()
                ->action(function ($record) {
                    $section = MenuSection::query()->where('parent_id', $this->record->id)->first();
                    if (Page::query()->where('parent_id', $section->parent_id)->exists()) {
                        Notification::make()->title(_actions('you_cant_delete_menu_section_with_items'))->danger()->send();
                    } else {
                        $parentId = $section->parent_id;
                        $section->delete();
                        Page::query()->where('id', $parentId)->delete();
                        Notification::make()->title(_actions('success'))->success()->send();

                        return redirect(ListStaticPages::getUrl());
                    }
                }),
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
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $section = MenuSection::query()->where('parent_id', $record->id)->first();
        $section->update($data);
        if ($record->is_categories) {
            Page::query()->where('parent_id', $record->id)->update([
                'layout_id' => $data['categories_layout_id'] ?? null,
            ]);
            $categories = Page::query()->where('parent_id', $record->id)->pluck('id')->toArray();
            Page::query()->whereIn('id', $categories)->update([
                'layout_id' => $data['categories_layout_id'] ?? null,
            ]);
        } else {
            Page::query()->where('parent_id', $record->id)->update([
                'layout_id' => $data['items_layout_id'] ?? null,
            ]);
        }

        return $record;
    }
}
