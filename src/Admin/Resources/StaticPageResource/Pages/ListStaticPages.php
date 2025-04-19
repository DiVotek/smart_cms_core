<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use SmartCms\Core\Admin\Base\Pages\BaseListRecords;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;

class ListStaticPages extends BaseListRecords
{
    protected static string $resource = StaticPageResource::class;

    public $menuSection;

    public $isCategories;

    public string $actionName;

    public function getBreadcrumbs(): array
    {
        if (config('shared.admin.breadcrumbs', false)) {
            return parent::getBreadcrumbs();
        }

        return [];
    }

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
        $actionName = request('activeTab') ?? '';
        $buttonName = _actions('create');
        $actionName = _actions('create').' '.$actionName;
        if ($this->menuSection) {
            if (! str_contains($actionName, _nav('categories'))) {
                $buttonName .= ' '._nav('item');
            } else {
                $buttonName .= ' '._nav('category');
            }
        }
        $this->actionName = $buttonName;
        $this->isCategories = $this->menuSection && $this->menuSection->is_categories && ! str_contains($actionName, _nav('categories'));
    }

    public function getTitle(): string|Htmlable
    {
        return $this->menuSection ? $this->menuSection->name : 'Static Pages';
    }

    protected function getResourceHeaderActions(): array
    {
        return [
            Actions\Action::make('Template')
                ->hidden(function () {
                    return (bool) $this->menuSection;
                })
                ->template()
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
                ->form(function ($form) {
                    return $form
                        ->schema([
                            Section::make('')->schema([
                                Schema::getTemplateBuilder()->label(_fields('template')),
                            ]),
                        ]);
                }),
            Actions\Action::make('create_menu_section')
                ->create()
                ->label(_actions('create_menu_section'))
                ->modal()
                ->hidden(function () {
                    return $this->menuSection;
                })
                ->modalWidth(MaxWidth::TwoExtraLarge)
                ->modalSubmitActionLabel(_actions('create'))
                ->form(function (Form $form) {
                    return $form->schema([
                        Schema::getReactiveName(),
                        Schema::getSlug(Page::getDb(), isRequired: false)->unique(Page::getDb(), 'slug', modifyRuleUsing: function ($rule, $get, $set) {
                            if (blank($get('slug'))) {
                                $set('slug', \Illuminate\Support\Str::slug($get('name')));
                            }

                            return $rule;
                        }),
                        Toggle::make('is_categories')->label(_fields('is_categories'))->default(false),
                    ]);
                })->action(function ($data) {
                    if (! isset($data['slug'])) {
                        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
                    }
                    $page = Page::query()->create([
                        'name' => $data['name'],
                        'slug' => $data['slug'],
                    ]);
                    MenuSection::query()->create([
                        'name' => $data['name'],
                        'parent_id' => $page->id,
                        'sorting' => MenuSection::query()->max('sorting') + 1,
                        'is_categories' => $data['is_categories'],
                    ]);
                    Notification::make(_actions('menu_section_created'))->success();

                    return redirect(ListStaticPages::getUrl());
                }),
            Actions\Action::make($this->actionName)
                ->create()
                ->label($this->actionName)
                ->modalWidth(MaxWidth::TwoExtraLarge)
                ->modalSubmitActionLabel(_actions('create'))
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
                        Schema::getSlug(Page::getDb(), false)->unique(Page::getDb(), 'slug', modifyRuleUsing: function ($rule, $get, $set) {
                            if (blank($get('slug'))) {
                                $set('slug', \Illuminate\Support\Str::slug($get('name')));
                            }

                            return $rule;
                        }),
                        Schema::getStatus(),
                        $parent_id,
                    ]);
                })
                ->action(function ($data) {
                    $slug = $data['slug'] ?? '';
                    if (! is_string($slug) || strlen($slug) < 2) {
                        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
                    }
                    if (Page::query()->where('slug', $data['slug'])->exists()) {
                        $data['slug'] = $data['slug'].'-'.\Illuminate\Support\Str::random(5);
                    }
                    if ($this->menuSection) {
                        $parent_id = $data['parent_id'] ?? null;
                        $parent = Page::query()->where('id', $parent_id)->first();
                        if ($parent && $parent->parent_id == null) {
                            $isCategories = $this->menuSection->is_categories;
                            if ($this->menuSection->is_categories) {
                                $data['layout_id'] = $this->menuSection->categories_layout_id ?? null;
                            } else {
                                $data['layout_id'] = $this->menuSection->items_layout_id ?? null;
                            }
                        } else {
                            $data['layout_id'] = $this->menuSection->items_layout_id ?? null;
                        }
                    }
                    Page::query()->create($data);
                    Notification::make('Page created successfully!')->success();
                }),
        ];
    }

    public function getTabs(): array
    {
        $menuSections = MenuSection::query()->get();
        $tabs = [
            'all' => Tab::make('All')->modifyQueryUsing(function (Builder $query) use ($menuSections) {
                $query->whereNull('parent_id')->whereNotIn('id', $menuSections->pluck('parent_id')->toArray());
            }),
        ];
        foreach ($menuSections as $section) {
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
