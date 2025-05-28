<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Table;
use Saade\FilamentAdjacencyList\Forms\Components\AdjacencyList;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\MenuResource\Pages;
use SmartCms\Core\Models\Menu;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Translate;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class MenuResource extends BaseResource
{
    public static string $resourceLabel = 'model_menu';

    public static ?string $resourceGroup = 'design-template';

    protected static ?string $model = Menu::class;

    protected static ?int $navigationSort = 3;

    public static function getFormSchema(Form $form): array
    {
        $types = [
            Page::class => 'Page',
            MenuSection::class => 'Menu Section',
            'custom' => 'Custom',
            'text' => 'Text',
        ];
        self::applyHook('menu_types', $types);
        $form = [
            Select::make('type')
                ->native(false)
                ->preload()
                ->required()
                ->reactive()
                ->options($types)->default(Page::class)->afterStateUpdated(function (Set $set, $state) {
                    $set('name', '');
                    $set('id', '');
                    $set('is_modified', 0);
                }),
            Group::make(function ($get) {
                $type = $get('type') ?? Page::class;
                switch ($type) {
                    case 'custom':
                        return [
                            TextInput::make('url')
                                ->url(function ($state) {
                                    return ! str_contains($state, '#') ? true : false;
                                })
                                ->live(onBlur: true)
                                ->label(_fields('url'))
                                ->required(),
                        ];
                    case Page::class:
                        return [
                            Select::make('id')->label(_fields('page'))->options(function () {
                                return Page::query()->where(function ($query) {
                                    $menuSections = MenuSection::query()->where('is_categories', 1)->pluck('parent_id')->toArray();
                                    $query->whereNull('parent_id')->orWhereIn('parent_id', $menuSections);
                                })->pluck('name', 'id')->toArray();
                            })->reactive()->afterStateUpdated(function (Set $set, $state) {
                                $page = Page::query()->where('id', $state)->first();
                                if ($page) {
                                    $set('name', $page->name);
                                    $translates = Translate::query()->where('entity_id', $state)->where('entity_type', Page::class)->get();
                                    foreach ($translates as $translate) {
                                        $set($translate->language->slug . '.name', $translate->value ?? '');
                                    }
                                }
                                $set('is_modified', 0);
                            }),
                        ];
                    case MenuSection::class:
                        return [
                            Select::make('id')->label(_fields('menu_section'))->options(MenuSection::query()->pluck('name', 'id')->toArray()),
                        ];
                    default:
                        $schema = [];
                        $schema = self::applyHook('menu_building', $type);
                        if (! is_array($schema)) {
                            return [];
                        }

                        return $schema;
                }
            }),
            TextInput::make('name')
                ->afterStateUpdated(function (Set $set, $state) {
                    $set('is_modified', 1);
                })
                ->label(_fields('name'))->suffixActions([
                    Action::make(_fields('translates'))
                        ->hidden(function () {
                            return ! is_multi_lang();
                        })
                        ->icon(function ($get) {
                            $languages = get_active_languages();
                            foreach ($languages as $language) {
                                if ($get($language->slug . '.name')) {
                                    return 'heroicon-o-check-circle';
                                }
                            }

                            return 'heroicon-o-exclamation-circle';
                        })->form(function ($form) {
                            $fields = [];
                            $languages = get_active_languages();
                            foreach ($languages as $language) {
                                $fields[] = TextInput::make($language->slug . '.name')->label(_fields('name') . ' (' . $language->name . ')');
                            }

                            return $form->schema($fields);
                        })->fillForm(function ($get) {
                            $translates = [];
                            $languages = get_active_languages();
                            foreach ($languages as $language) {
                                $translates[$language->slug] = [
                                    'name' => $get($language->slug . '.name') ?? '',
                                ];
                            }

                            return $translates;
                        })->action(function ($set, $data) {
                            foreach (get_active_languages() as $lang) {
                                $name = $data[$lang->slug]['name'] ?? '';
                                if ($name) {
                                    $set($lang->slug . '.name', $name);
                                    $set('is_modified', 1);
                                } else {
                                    $set($lang->slug . '.name', '');
                                }
                            }
                        }),
                ])
                ->required(),
        ];
        foreach (get_active_languages() as $lang) {
            $form[] = Hidden::make($lang->slug . '.name');
        }
        $form[] = Hidden::make('is_modified')->default(0);

        return [
            Schema::getName(),
            AdjacencyList::make('value')->columnSpanFull()
                ->maxDepth(2)
                ->labelKey('name')
                ->label(_fields('menu'))
                ->modal(true)
                ->form($form),
        ];
    }

    public static function getTableColumns(Table $table): array
    {
        return [
            TableSchema::getName(),
            TableSchema::getUpdatedAt(),
        ];
    }

    public static function getResourcePages(): array
    {
        return [
            'index' => Pages\ManageMenu::route('/'),
        ];
    }
}
