<?php

namespace SmartCms\Core\Services;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Saade\FilamentAdjacencyList\Forms\Components\AdjacencyList;
use Schmeits\FilamentCharacterCounter\Forms\Components\RichEditor;
use Schmeits\FilamentCharacterCounter\Forms\Components\Textarea;
use Schmeits\FilamentCharacterCounter\Forms\Components\TextInput as ComponentsTextInput;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Models\Translate;

class Schema
{
    public static function getName(bool $isRequired = true): TextInput
    {
        return TextInput::make('name')
            ->label(_fields('name'))
            ->helperText(__('The field will be displayed on the page'))
            ->string()
            ->reactive()
            ->required($isRequired);
    }

    public static function getSlug(?string $table = null, bool $isRequired = true): TextInput
    {
        $slug = TextInput::make('slug')
            ->label(_fields('slug'))
            ->string()
            ->readOnly()
            ->required($isRequired)
            ->helperText(__('Slug will be generated automatically from title of any language'))
            ->default('')->hintActions([
                Action::make('clear_slug')
                    ->label(_actions('clear_slug'))
                    ->requiresConfirmation()
                    ->icon('heroicon-o-trash')
                    ->action(function (Set $set, $state) {
                        $set('slug', null);
                    }),
                Action::make('generate_slug')
                    ->label(_actions('generate_slug'))
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Set $set, $get) {
                        $name = $get('name') ?? '';
                        $set('slug', Str::slug($name));
                    }),
            ]);
        if ($table) {
            $slug->unique(table: $table, ignoreRecord: true);
        }

        return $slug;
    }

    public static function getReactiveName(bool $isRequired = true): TextInput
    {
        return TextInput::make('name')
            ->label(_fields('name'))
            ->string()
            // ->reactive()
            ->required($isRequired);
        // ->live(debounce: 1000)
        // ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state, ?Model $record) {
        //     $isSlugEdit = true;
        //     if ($record && $record->slug) {
        //         $isSlugEdit = false;
        //     }
        //     if ($get('title') == $old) {
        //         $set('title', $state);
        //     }
        //     if ($get('heading') == $old) {
        //         $set('heading', $state);
        //     }
        //     if (($get('slug') ?? '') !== Str::slug($old) && $get('slug') !== null) {
        //         return;
        //     }
        //     if ($get('slug') == null && ! $isSlugEdit) {
        //         $isSlugEdit = true;
        //     }
        //     if ($isSlugEdit) {
        //         $set('slug', Str::slug($state));
        //     }
        // })
    }

    public static function getImage(string $name = 'image', bool $isMultiple = false, string $path = '', string $filaname = ''): FileUpload
    {
        $upload = FileUpload::make($name)
            ->image()
            ->imageEditor()
            ->helperText(__('Upload the image of the record'))
            ->imageEditorAspectRatios([
                '16:9',
                '4:3',
                '1:1',
            ])
            ->reorderable()
            ->directory($path ?? 'images')
            ->openable()
            ->optimize('webp')
            ->disk('public')
            ->multiple($isMultiple);
        if ($filaname) {
            $upload->fileName($filaname);
        }

        return $upload;
    }

    public static function getSorting(): TextInput
    {
        return TextInput::make('sorting')
            ->label(_fields('sorting'))
            ->numeric()->default(1)
            ->helperText(__('The lower the number, the higher the record will be displayed'));
    }

    public static function getStatus(): Toggle
    {
        return Toggle::make('status')
            ->label(_fields('status'))
            ->required()
            ->default(Status::ON)
            ->helperText(__('If status is off, the record will not be displayed on the site'));
    }

    public static function getRepeater(string $name = 'value'): Repeater
    {
        return Repeater::make($name)
            ->addActionLabel(_fields('repeater_add'))
            ->reorderableWithButtons()
            ->collapsible()
            ->cloneable();
    }

    public static function getSelect(string $name = 'select', array $options = []): Select
    {
        return Select::make($name)
            ->options($options)
            ->native(false)->searchable(true);
    }

    public static function getLinkBuilder(string $name): AdjacencyList
    {
        $types = [
            Page::class => 'Page',
            MenuSection::class => 'Menu Section',
            'custom' => 'Custom',
        ];
        Event::dispatch('cms.admin.menu.form', [&$types]);
        $form = [
            Select::make('type')
                ->native(false)
                ->preload()
                ->required()
                ->reactive()
                ->options($types)->default(Page::class)->afterStateUpdated(function (Set $set, $state) {
                    $set('name', '');
                    $set('id', '');
                }),
            Group::make(function ($get) {
                $type = $get('type') ?? Page::class;
                switch ($type) {
                    case 'custom':
                        return [
                            TextInput::make('url')
                                ->url()
                                ->label(_fields('url'))
                                ->required(),
                        ];
                    case Page::class:
                        return [
                            Select::make('id')->label(_fields('page'))->options(function () {
                                return Page::query()->whereNull('parent_id')->pluck('name', 'id')->toArray();
                            })->reactive()->afterStateUpdated(function (Set $set, $state) {
                                $page = Page::query()->where('id', $state)->first();
                                if ($page) {
                                    $set('name', $page->name);
                                    $translates = Translate::query()->where('entity_id', $state)->where('entity_type', Page::class)->get();
                                    foreach ($translates as $translate) {
                                        $set($translate->language->slug.'.name', $translate->value ?? '');
                                    }
                                }
                            }),
                        ];
                    case MenuSection::class:
                        return [
                            Select::make('id')->label(_fields('menu_section'))->options(MenuSection::query()->pluck('name', 'id')->toArray()),
                        ];
                    default:
                        $schema = [];
                        Event::dispatch('cms.admin.menu.building', [$type, &$schema]);
                        if (! is_array($schema)) {
                            return [];
                        }

                        return $schema;
                }
            }),
            TextInput::make('name')
                ->label(_fields('name'))->suffixActions([
                    Action::make(_fields('translates'))
                        ->hidden(function () {
                            return ! is_multi_lang();
                        })
                        ->icon(function ($get) {
                            $languages = get_active_languages();
                            foreach ($languages as $language) {
                                if ($get($language->slug.'.name')) {
                                    return 'heroicon-o-check-circle';
                                }
                            }

                            return 'heroicon-o-exclamation-circle';
                        })->form(function ($form) {
                            $fields = [];
                            $languages = get_active_languages();
                            foreach ($languages as $language) {
                                $fields[] = TextInput::make($language->slug.'.name')->label(_fields('name').' ('.$language->name.')');
                            }

                            return $form->schema($fields);
                        })->fillForm(function ($get) {
                            $translates = [];
                            $languages = get_active_languages();
                            foreach ($languages as $language) {
                                $translates[$language->slug] = [
                                    'name' => $get($language->slug.'.name') ?? '',
                                ];
                            }

                            return $translates;
                        })->action(function ($set, $data) {
                            foreach (get_active_languages() as $lang) {
                                $name = $data[$lang->slug]['name'] ?? '';
                                if ($name) {
                                    $set($lang->slug.'.name', $name);
                                } else {
                                    $set($lang->slug.'.name', '');
                                }
                            }
                        }),
                ])
                ->required(),
            Toggle::make('as_link')
                ->label(_fields('as_link'))
                ->default(true),
        ];
        foreach (get_active_languages() as $lang) {
            $form[] = Hidden::make($lang->slug.'.name');
        }

        return AdjacencyList::make($name)->columnSpanFull()
            ->maxDepth(2)
            ->labelKey('name')
            ->modal(true)
            ->form($form);
    }

    public static function getTemplateBuilder(string $name = 'template'): Repeater
    {
        $options = TemplateSection::query()->pluck('name', 'id')->toArray();

        return self::getRepeater($name)
            ->label(_fields('template'))
            ->helperText(_hints('template'))
            ->schema([
                self::getSelect('template_section_id', $options)
                    ->label(_fields('section'))
                    ->preload()->required(),
                Hidden::make('value')->default([]),
            ])->default([]);
    }

    public static function getSeoForm(array $existed_languages = []): array
    {
        $languages = get_active_languages()->whereNotIn('id', $existed_languages)->pluck('id')->toArray();
        $language = Hidden::make('language_id');
        if (is_multi_lang()) {
            $language = Schema::getSelect('language_id')->relationship('language', 'name', function ($query) use ($languages) {
                $query->whereIn('id', $languages);
            })->preload();
        }
        $language = $language->default($languages[0] ?? main_lang_id());

        return [
            Section::make('')->schema([
                $language,
                ComponentsTextInput::make('title')
                    ->label(_fields('seo_title'))
                    ->required()
                    ->translatable()
                    ->rules('string', 'max:255')
                    ->characterLimit(255)
                    ->maxLength(255),
                ComponentsTextInput::make('heading')
                    ->label(_fields('seo_heading'))
                    ->translatable()
                    ->rules('string', 'max:255')
                    ->characterLimit(255)
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(_fields('seo_description'))
                    ->required()
                    ->rules('string', 'max:255')
                    ->translatable()
                    ->characterLimit(255)
                    ->maxLength(255),
                Textarea::make('summary')
                    ->label(_fields('seo_summary'))
                    ->translatable()
                    ->rules('string', 'max:500')
                    ->maxLength(500),
                RichEditor::make('content')
                    ->label(_fields('seo_content'))
                    ->translatable()
                    ->rules('string'),
            ]),
        ];
    }
}
