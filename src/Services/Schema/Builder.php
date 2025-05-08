<?php

namespace SmartCms\Core\Services\Schema;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use SmartCms\Core\Models\Form;
use SmartCms\Core\Models\Menu;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Traits\HasHooks;

class Builder
{
    use HasHooks;

    public $languages;

    public FieldSchema $field;

    public const AVAILABLE_TYPES = [
        'text', // multi-lang
        'number', // multi-lang
        'bool',
        'image', // multi-lang
        'file',

        'heading', // multi-lang
        'description', // multi-lang
        'textarea',

        'socials',
        'phone',
        'phones',
        'email',
        'emails',
        'address',
        'addresses',

        'menu',
        'form',
        'page',
        'pages',
        'link',

        'array',  // repeater
    ];

    public static function make(FieldSchema $field): array
    {
        return (new self($field))->invoke();
    }

    public function __construct(FieldSchema $field)
    {
        $this->languages = get_active_languages();
        $this->field = $field;
    }

    public function invoke(): array
    {
        $component = null;
        if (in_array($this->field->type, self::AVAILABLE_TYPES)) {
            switch ($this->field->type) {
                case 'number':
                    $component = $this->getText(true);
                    break;
                case 'bool':
                    $component = Toggle::make($this->field->name)->label($this->field->label)->required($this->field->required);
                    break;
                case 'image':
                    $component = $this->getImage();
                    break;
                case 'file':
                    $component = $this->getFile();
                    break;

                case 'heading':
                    $component = $this->getHeading();
                    break;
                case 'description':
                    $component = $this->getDescription();
                    break;

                case 'socials':
                    $component = $this->getSocials();
                    break;
                case 'phone':
                    $component = $this->getPhones(false);
                    break;
                case 'phones':
                    $component = $this->getPhones();
                    break;
                case 'email':
                    $component = $this->getEmails(false);
                    break;
                case 'emails':
                    $component = $this->getEmails();
                    break;
                case 'address':
                    $component = $this->getAddresses(false);
                    break;
                case 'addresses':
                    $component = $this->getAddresses();
                    break;
                case 'menu':
                    $component = $this->getMenu();
                    break;
                case 'form':
                    $component = $this->getForm();
                    break;
                case 'page':
                    $component = $this->getPage();
                    break;
                case 'pages':
                    $component = $this->getPages();
                    break;
                case 'link':
                    $component = $this->getLink();
                    break;

                case 'array':
                    $component = $this->getArray();
                    break;
                case 'textarea':
                    $component = $this->getTextarea();
                    break;
                default:
                    $component = $this->getText();
            }
        } else {
            $this->applyHook('build', $component, $this->field);
        }
        if (! $component instanceof Component) {
            dd($component, $this->field);
            throw new \Exception('Field type not found');
        }
        if ($component instanceof Field) {
            $component = $component->required($this->field->required)->label($this->field->label);
        }
        $fields = [$component];
        if (is_multi_lang() && $this->field->type != 'array') {
            foreach ($this->languages as $lang) {
                if (str_contains($this->field->name, '.')) {
                    $langName = str_replace('.', '.'.$lang->slug.'.', $this->field->name);
                } else {
                    $langName = $lang->slug.'.'.$this->field->name;
                }
                $fields[] = Hidden::make($langName);
            }
        }

        return $fields;
    }

    public function getText(bool $isNumeric = false): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = TextInput::make($lang->slug)->label($lang->name)
                ->numeric($isNumeric);
        }

        return Section::make($this->field->label)->compact()->schema([
            TextInput::make($this->field->name)->hiddenLabel()
                ->numeric($isNumeric)
                ->required($this->field->required)
                ->suffixAction(
                    $this->getTranslateAction($fields)
                ),
        ]);
    }

    public function getImage(): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = Schema::getImage($lang->slug)->label($lang->name);
        }

        return Section::make($this->field->label)->compact()->schema([
            Schema::getImage($this->field->name)->hiddenLabel()->required($var['required'] ?? true)->hintAction(self::getTranslateAction($fields, $this->field->name)),
        ]);

        return Schema::getImage($this->field->name)->label($this->field->label)->required($var['required'] ?? true)->hintAction(self::getTranslateAction($fields, $this->field->name));
    }

    public function getHeading(): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = TextInput::make($lang->slug)->label($lang->name);
        }

        return Section::make($this->field->label)->compact()
            ->schema([
                Grid::make(2)->schema([
                    Hidden::make($this->field->name.'.use_custom')->default(false),
                    Hidden::make($this->field->name.'.use_page_name')->default(false),
                    Hidden::make($this->field->name.'.use_page_heading')->default(true),
                    Select::make($this->field->name.'.heading_type')
                        ->label(_fields('heading_type'))
                        ->options([
                            'h1' => 'H1',
                            'h2' => 'H2',
                            'h3' => 'H3',
                            'none' => 'None',
                        ])
                        ->required()
                        ->default('h2')->formatStateUsing(function ($state) {
                            return $state ?? 'h2';
                        }),
                    Select::make($this->field->name.'.scope')->options([
                        'heading' => 'Heading',
                        'name' => 'Name',
                        'custom' => 'Custom',
                    ])->live()->afterStateUpdated(function ($state, callable $set) {
                        if ($state == 'custom') {
                            $set($this->field->name.'.use_custom', true);
                            $set($this->field->name.'.use_page_name', false);
                            $set($this->field->name.'.use_page_heading', false);

                            return;
                        }
                        if ($state == 'heading') {
                            $set($this->field->name.'.use_custom', false);
                            $set($this->field->name.'.use_page_name', false);
                            $set($this->field->name.'.use_page_heading', true);

                            return;
                        }
                        $set($this->field->name.'.use_custom', false);
                        $set($this->field->name.'.use_page_heading', false);
                        $set($this->field->name.'.use_page_name', true);
                    })
                        ->required()->default('heading')->formatStateUsing(function ($state) {
                            return $state ?? 'heading';
                        })->live(),
                ]),
                TextInput::make($this->field->name.'.heading')->label(_fields('heading'))->required()->hidden(function ($get) {
                    return $get($this->field->name.'.scope') != 'custom';
                })->columnSpanFull()->suffixAction(self::getTranslateAction($fields, $this->field->name)),
            ]);

        return TextInput::make($this->field->name)->label($this->field->label)->required($this->field->required);
    }

    public function getDescription(): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = Textarea::make($lang->slug)->label($lang->name);
        }

        return Section::make($this->field->label)->compact()->schema([
            Hidden::make($this->field->name.'.is_custom')->default(false),
            Hidden::make($this->field->name.'.is_description')->default(true),
            Hidden::make($this->field->name.'.is_summary')->default(false),
            Select::make($this->field->name.'.scope')->label(_fields('scope'))->live()->options([
                'description' => _fields('description'),
                'summary' => _fields('summary'),
                'custom' => 'Custom',
            ])->required()->default('description')->formatStateUsing(function ($state) {
                return $state ?? 'description';
            })->afterStateUpdated(function ($state, callable $set) {
                if ($state == 'custom') {
                    $set($this->field->name.'.is_custom', true);
                    $set($this->field->name.'.is_description', false);
                    $set($this->field->name.'.is_summary', false);

                    return;
                }
                if ($state == 'summary') {
                    $set($this->field->name.'.is_custom', false);
                    $set($this->field->name.'.is_description', false);
                    $set($this->field->name.'.is_summary', true);

                    return;
                }
                $set($this->field->name.'.is_custom', false);
                $set($this->field->name.'.is_summary', false);
                $set($this->field->name.'.is_description', true);
            }),
            Textarea::make($this->field->name.'.description')
                ->label(_fields('description'))->required()
                ->hidden(function ($get) {
                    return $get($this->field->name.'.scope') != 'custom';
                })->columnSpanFull()->rows(3)->hintAction(self::getTranslateAction($fields, $this->field->name)),
        ]);

        return Grid::make()->columns(3)->schema([
            Toggle::make($this->field->name.'.is_description')
                ->label(_fields('use_page_description'))->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set($this->field->name.'.is_custom', false);
                        $set($this->field->name.'.is_summary', false);
                    }
                }),
            Toggle::make($this->field->name.'.is_summary')
                ->label(_fields('use_page_summary'))->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set($this->field->name.'.is_custom', false);
                        $set($this->field->name.'.is_description', false);
                    }
                }),
            Toggle::make($this->field->name.'.is_custom')
                ->label(_fields('use_custom'))->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set($this->field->name.'.is_summary', false);
                        $set($this->field->name.'.is_description', false);
                    }
                }),
            Textarea::make($this->field->name.'.description')
                ->label(_fields('description'))->required()
                ->hidden(function ($get) {
                    return ! $get($this->field->name.'.is_custom');
                })->columnSpanFull()->rows(3)->hintAction(self::getTranslateAction($fields, $this->field->name)),
        ]);

        return Textarea::make($this->field->name)->label($this->field->label)->required($this->field->required)->hintAction(self::getTranslateAction($fields, $this->field->name.'.description'));
    }

    public function getForm(): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options(Form::query()->pluck('name', 'id')->toArray())
                ->required($this->field->required);
        }

        return Section::make($this->field->label)->compact()->schema([
            Select::make($this->field->name)->hiddenLabel()->options(Form::query()->pluck('name', 'id')->toArray())->required($this->field->required),
        ]);
    }

    public function getPhones($is_multiple = true): Component
    {
        $options = phones();

        return Select::make($this->field->name)->label($this->field->label)
            ->options($options)
            ->native(false)
            ->required($this->field->required)
            ->multiple($is_multiple);
    }

    public function getEmails(bool $is_multiple = true): Component
    {
        $options = emails();

        return Select::make($this->field->name)
            ->label($this->field->label)
            ->options($options)
            ->required($this->field->required)
            ->multiple($is_multiple);
    }

    public function getAddresses(bool $is_multiple = true): Component
    {
        $options = addresses();

        return Select::make($this->field->name)->label($this->field->label)
            ->options($options)
            ->required($this->field->required)
            ->multiple($is_multiple);
    }

    public function getSocials(): Component
    {
        return Select::make($this->field->name)->label($this->field->label)->options(social_names())->multiple()->required($this->field->required);
    }

    public function getMenu(): Component
    {
        return Schema::getSelect($this->field->name)->label($this->field->label)->options(Menu::query()->pluck('name', 'id')->toArray())->required($this->field->required);
    }

    public function getTranslateAction(array $schema): Action
    {
        $name = $this->field->name;
        if (! is_multi_lang()) {
            return Action::make('translate')->label(_actions('translate'))->hidden();
        }
        $languages = $this->languages;

        return Action::make(_actions('translate'))
            ->icon('heroicon-m-language')
            ->modalHeading(_actions('translate').' '.$this->field->label)
            ->form($schema)
            ->badge(function ($get) use ($name, $languages) {
                $counter = 0;
                foreach ($languages as $lang) {
                    $langName = str_contains($name, '.') ? str_replace('.', '.'.$lang->slug.'.', $name) : $lang->slug.'.'.$name;
                    $placeholder = $get($langName) ?? null;
                    if ($placeholder && strlen($placeholder) > 0) {
                        $counter++;
                    }
                }

                return $counter;
            })
            ->mountUsing(function ($form, $get) use ($name, $languages) {
                $langData = [];
                foreach ($languages as $lang) {
                    $langName = str_contains($name, '.') ? str_replace('.', '.'.$lang->slug.'.', $name) : $lang->slug.'.'.$name;
                    $langData[$lang->slug] = $get($langName);
                }
                $form->fill($langData);
            })
            ->action(function ($data, $set, $get) use ($name) {
                foreach ($data as $key => $value) {
                    $langName = str_contains($name, '.') ? str_replace('.', '.'.$key.'.', $name) : $key.'.'.$name;
                    $set($langName, $value);
                }
            });
    }

    public function getPage(): Component
    {
        $menuSections = MenuSection::query()->pluck('name', 'parent_id')->toArray();
        $menuSections['null'] = _fields('without_section');

        return Section::make($this->field->label)->compact()->schema([
            Select::make($this->field->name.'.parent_id')->label(_fields('page_type'))->options($menuSections)->required($this->field->required)->live()->afterStateUpdated(function ($state, callable $set) {
                $set($this->field->name.'.id', null);
            })->formatStateUsing(function ($state) {
                return $state ?? 'null';
            }),
            Select::make($this->field->name.'.id')->label(_fields('page_part'))->options(function ($get) {
                $parent = $get($this->field->name.'.parent_id') ?? null;
                if ($parent === 'null') {
                    return Page::query()->whereNull('parent_id')->pluck('name', 'id')->toArray();
                }

                return Page::query()->where('parent_id', $get($this->field->name.'.parent_id') ?? null)->pluck('name', 'id')->toArray();
            })->required($this->field->required)->live(),
        ])->columns(2);
    }

    public function getPages(): Component
    {
        $menuSections = MenuSection::query()->pluck('name', 'parent_id')->toArray();
        $menuSections['null'] = _fields('without_section');

        return Section::make($this->field->label)->compact()->schema([
            Grid::make()->columns(2)->schema([
                Select::make($this->field->name.'.type')->label(_fields('page_type'))->options([
                    'items' => _fields('items'),
                    'categories' => _fields('categories'),
                ])->required()->live()->formatStateUsing(function ($state) {
                    return $state ?? 'items';
                })->afterStateUpdated(function ($state, callable $set) {
                    $set($this->field->name.'.parent_id', null);
                }),
                Select::make($this->field->name.'.parent_id')->label(_fields('menu_section'))->options(function ($get) {
                    $type = $get($this->field->name.'.type');
                    $isCategories = false;
                    if ($type == 'categories') {
                        $isCategories = true;
                    }

                    return MenuSection::query()->where('is_categories', $isCategories)->pluck('name', 'parent_id')->toArray();
                })->required()->live(),
                TextInput::make($this->field->name.'.limit')->numeric()->label(_fields('limit'))->required()->live()->default(5),
                Select::make($this->field->name.'.scope')->label(_fields('scope'))->options([
                    'last' => 'Last',
                    'popular' => 'Popular',
                    'random' => 'Random',
                    'by_hand' => 'By hand',
                ])->required()->native(false)->default('last')->formatStateUsing(function ($state) {
                    return $state ?? 'last';
                }),
            ]),
            Select::make($this->field->name.'.ids')->label(_fields('page_parts'))->options(function ($get) {
                $parentId = (int) $get($this->field->name.'.parent_id');
                if (! $parentId) {
                    return [];
                }

                return Page::query()->where('parent_id', $parentId)->pluck('name', 'id')->toArray();
            })
                ->multiple()
                ->required(function ($get) {
                    return $get($this->field->name.'.scope') == 'by_hand';
                })
                ->live()
                ->hidden(function ($get) {
                    return $get($this->field->name.'.scope') != 'by_hand';
                })->columnSpanFull(),
        ])->columnSpanFull();
    }

    public function getFile() {}

    public function getArray(): Component
    {
        $fields = [];
        foreach ($this->field->options as $option) {
            $fieldSchema = ArrayToField::make($option);
            $fields = array_merge(self::make($fieldSchema), $fields);
        }

        return Section::make($this->field->label)->compact()->schema([
            Repeater::make($this->field->name)->hiddenLabel()->schema(array_reverse($fields))
                ->cloneable()
                ->default([])
                ->required($this->field->required),
        ]);
    }

    public function getTextarea(): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = RichEditor::make($lang->slug)->label($lang->name);
        }

        return RichEditor::make($this->field->name)->hintAction(self::getTranslateAction($fields, $this->field->name))->label($this->field->label)->required($this->field->required)->toolbarButtons([
            'bold',
            'underline',
            'bulletList',
            'numberedList',
            'link',
        ]);
    }

    public function getLink(): Component
    {
        $urlFields = [];
        foreach ($this->languages as $lang) {
            $urlFields[] = TextInput::make($lang->slug)->label($lang->name)->url();
        }

        return Section::make($this->field->label)->compact()->schema([
            TextInput::make($this->field->name.'.url')->label(_fields('url'))->required()->suffixAction(self::getTranslateAction($urlFields, $this->field->name))->url(),
            Grid::make()->columns(3)->schema([
                Toggle::make($this->field->name.'.is_internal')->label(_fields('is_internal'))->default(false)->inline(false),
                Toggle::make($this->field->name.'.new_tab')->label(_fields('new_tab'))->default(false)->inline(false),
                Toggle::make($this->field->name.'.is_indexable')->label(_fields('is_indexable'))->default(false)->inline(false),
            ]),
        ])->columns(1);
    }
}
