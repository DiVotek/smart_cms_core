<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;

class ListStaticPages extends ListRecords
{
    protected static string $resource = StaticPageResource::class;

    public $menuSection;

    public $isCategories;

    public string $actionName;

    public function mount(): void
    {
        parent::mount();

        $activeTab = request('activeTab');
        foreach (MenuSection::all() as $section) {
            if ($section->is_categories) {
                if ($section->name._nav('categories') == $activeTab) {
                    $this->menuSection = $section;
                    break;
                } else {
                    if ($section->name == $activeTab) {
                        $this->menuSection = $section;
                        break;
                    }
                }
            } else {
                if ($section->name == $activeTab) {
                    $this->menuSection = $section;
                    break;
                }
            }
        }
        $actionName = request('activeTab') ?? 'Static Page';
        $buttonName = _actions('create');
        $actionName = _actions('create').' '.$actionName;
        if (! str_contains($actionName, _nav('categories'))) {
            $buttonName .= ' '._nav('item');
        } else {
            $buttonName .= ' '._nav('category');
        }
        $this->actionName = $buttonName;
        $this->isCategories = $this->menuSection && $this->menuSection->is_categories && ! str_contains($actionName, _nav('categories'));
    }

    public function getTitle(): string|Htmlable
    {
        return $this->menuSection ? $this->menuSection->name : 'Static Pages';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make(_actions('help'))
                ->iconButton()
                ->icon('heroicon-o-question-mark-circle')
                ->modalDescription(_hints('help.page'))
                ->modalFooterActions([]),
            Actions\Action::make('Template')
                ->label(_actions('template'))
                ->slideOver()
                ->icon('heroicon-o-cog')
                ->fillForm(function (): array {
                    return [
                        'template' => _settings('static_page_template', []),
                    ];
                })
                ->action(function (array $data): void {
                    setting([
                        sconfig('static_page_template') => $data['template'],
                    ]);
                })
                ->hidden(function () {
                    return (bool) request('parent');
                })
                ->form(function ($form) {
                    return $form
                        ->schema([
                            Section::make('')->schema([
                                Schema::getTemplateBuilder()->label(_fields('template')),
                            ]),
                        ]);
                }),
            Actions\Action::make($this->actionName)
                ->label($this->actionName)
                ->form(function ($form) {
                    if ($this->menuSection) {
                        if ($this->isCategories) {
                            $parent_id = Select::make('parent_id')
                                ->options(Page::query()->where('parent_id', $this->menuSection->parent_id)->pluck('name', 'id')->toArray())->required();
                        } else {
                            $parent_id = Hidden::make('parent_id')->default($this->menuSection->parent_id);
                        }
                    } else {
                        $parent_id = Hidden::make('parent_id')->default(null);
                    }

                    return $form->schema([
                        Schema::getReactiveName(),
                        Schema::getSlug(Page::getDb()),
                        Schema::getStatus(),
                        $parent_id,
                    ]);
                })
                ->action(function ($data) {
                    Page::query()->create($data);
                    Notification::make('Page created successfully!')->success();
                }),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')->modifyQueryUsing(fn (Builder $query) => $query->whereNull('parent_id')),
        ];
        foreach (MenuSection::query()->get() as $section) {
            if ($section->is_categories) {
                $name = $section->name._nav('categories');
                $tabs[$name] = Tab::make($name)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('parent_id', $section->parent_id));
                $categories = Page::query()->where('parent_id', $section->parent_id)->pluck('id')->toArray();
                $tabs[$section->name] = Tab::make($section->name.' '._nav('item'))
                    ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('parent_id', $categories));
            } else {
                $tabs[$section->name] = Tab::make($section->name.' '._nav('item'))
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('parent_id', $section->parent_id));
            }
        }

        return $tabs;
    }
}
