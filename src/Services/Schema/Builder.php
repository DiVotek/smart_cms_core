<?php

namespace SmartCms\Core\Services\Schema;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Event;
use SmartCms\Core\Models\Form;
use SmartCms\Core\Models\Menu;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;

class Builder
{
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

        'socials',
        'phone',
        'phones',
        'email',
        'emails',
        'address',
        'addresses',
        'schedule',
        'schedules',

        'menu',
        'form',
        'page',
        'pages',

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
                case 'schedule':
                    $component = $this->getSchedules(false);
                    break;
                case 'schedules':
                    $component = $this->getSchedules();
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

                case 'array':
                    $component = $this->getArray();
                    break;
                default:
                    $component = $this->getText();
            }
        } else {
            Event::dispatch('cms.admin.schema.build', [$this->field, &$component]);
        }
        if (! $component instanceof Component) {
            throw new \Exception('Field type not found');
        }
        if ($component instanceof Field) {
            $component = $component->required($this->field->required)->label($this->field->label);
        }
        $fields = [$component];
        if (is_multi_lang() && $this->field->type != 'array') {
            foreach ($this->languages as $lang) {
                if (str_contains($this->field->name, '.')) {
                    $langName = str_replace('.', '.' . $lang->slug . '.', $this->field->name);
                } else {
                    $langName = $lang->slug . '.' . $this->field->name;
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
                ->numeric($isNumeric)
                ->required($this->field->required);
        }

        return TextInput::make($this->field->name)->label($this->field->label)
            ->numeric($isNumeric)
            ->required($this->field->required)
            ->suffixAction(
                $this->getTranslateAction($fields)
            );
    }

    public function getImage(): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = Schema::getImage($lang->slug)->label($lang->name)->required($this->field->required);
        }

        return Schema::getImage($this->field->name)->label($this->field->label)->required($var['required'] ?? true)->hintAction(self::getTranslateAction($fields, $this->field->name));
    }

    public function getHeading(): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = Textarea::make($lang->slug)->label($lang->name)->required($this->field->required);
        }

        return Fieldset::make($this->field->label)
            ->schema([
                Radio::make($this->field->name . '.heading_type')
                    ->options([
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'none' => 'None',
                    ])
                    ->required()
                    ->default('h2')->inline(),
                Grid::make()->columns(3)->schema([
                    Toggle::make($this->field->name . '.use_page_heading')->label(_fields('use_page_heading'))->default(true)->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set($this->field->name . '.use_page_name', false);
                            $set($this->field->name . '.use_custom', false);
                        }
                    }),
                    Toggle::make($this->field->name . '.use_page_name')->label(_fields('use_page_name'))->default(false)->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set($this->field->name . '.use_page_heading', false);
                            $set($this->field->name . '.use_custom', false);
                        }
                    }),
                    Toggle::make($this->field->name . '.use_custom')->label(_fields('use_custom'))->default(false)->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set($this->field->name . '.use_page_heading', false);
                            $set($this->field->name . '.use_page_name', false);
                        }
                    }),
                ])->columnSpan(3),
                Textarea::make($this->field->name . '.heading')->label(_fields('heading'))->required()->hidden(function ($get) {
                    return ! $get($this->field->name . '.use_custom');
                })->columnSpanFull()->rows(3)->hintAction(self::getTranslateAction($fields, $this->field->name)),
            ]);

        return TextInput::make($this->field->name)->label($this->field->label)->required($this->field->required);
    }

    public function getDescription(): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = Textarea::make($lang->slug)->label($lang->name)->required($this->field->required);
        }

        return Grid::make()->columns(3)->schema([
            Toggle::make($this->field->name . '.is_description')
                ->label(_fields('use_page_description'))->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set($this->field->name . '.is_custom', false);
                        $set($this->field->name . '.is_summary', false);
                    }
                }),
            Toggle::make($this->field->name . '.is_summary')
                ->label(_fields('use_page_summary'))->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set($this->field->name . '.is_custom', false);
                        $set($this->field->name . '.is_description', false);
                    }
                }),
            Toggle::make($this->field->name . '.is_custom')
                ->label(_fields('use_custom'))->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set($this->field->name . '.is_summary', false);
                        $set($this->field->name . '.is_description', false);
                    }
                }),
            Textarea::make($this->field->name . '.description')
                ->label(_fields('description'))->required()
                ->hidden(function ($get) {
                    return ! $get($this->field->name . '.is_custom');
                })->columnSpanFull()->rows(3)->hintAction(self::getTranslateAction($fields, $this->field->name)),
        ]);

        return Textarea::make($this->field->name)->label($this->field->label)->required($this->field->required)->hintAction(self::getTranslateAction($fields, $this->field->name . '.description'));
    }

    public function getForm(): Component
    {
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options(Form::query()->pluck('name', 'id')->toArray())
                ->required($this->field->required);
        }

        return Select::make($this->field->name)->label($this->field->label)->options(Form::query()->pluck('name', 'id')->toArray())->required($this->field->required);
    }

    public function getPhones($is_multiple = true): Component
    {
        $options = phones();
        $fields = [];
        foreach ($this->languages as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options($options)
                ->native(false)
                ->multiple($is_multiple)
                ->required($this->field->required);
        }

        return Select::make($this->field->name)->label($this->field->label)
            ->options($options)
            ->native(false)
            ->required($this->field->required)
            ->multiple($is_multiple)
            ->suffixAction(
                $this->getTranslateAction($fields),
            );
    }

    public function getEmails(bool $is_multiple = true): Component
    {
        $fields = [];
        $options = emails();
        foreach ($this->languages as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)->options($options)->required($this->field->required)->multiple($is_multiple);
        }

        return Select::make($this->field->name)
            ->label($this->field->label)
            ->options($options)
            ->required($this->field->required)
            ->multiple($is_multiple)
            ->suffixAction(
                $this->getTranslateAction($fields)
            );
    }

    public function getAddresses(bool $is_multiple = true): Component
    {
        $fields = [];
        $options = addresses();
        foreach ($this->languages as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options($options)
                ->multiple($is_multiple)
                ->required($this->field->required);
        }

        return Select::make($this->field->name)->label($this->field->label)
            ->options($options)
            ->required($this->field->required)
            ->multiple($is_multiple)
            ->suffixAction(
                $this->getTranslateAction($fields),
            );
    }

    public function getSchedules($is_multiple = true): Component
    {
        $fields = [];
        $options = schedules();
        foreach ($this->languages as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options($options)
                ->multiple($is_multiple)
                ->required($this->field->required);
        }

        return Select::make($this->field->name)->label($this->field->label)
            ->options($options)
            ->required($this->field->required)
            ->multiple($is_multiple)
            ->suffixAction(
                $this->getTranslateAction($fields),
            );
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
            return Action::make(_actions('translate'))->hidden();
        }
        $languages = $this->languages;

        return Action::make(_actions('translate'))
            ->icon('heroicon-m-language')
            ->form($schema)
            ->mountUsing(function ($form, $get) use ($name, $languages) {
                $langData = [];
                foreach ($languages as $lang) {
                    $langName = str_contains($name, '.') ? str_replace('.', '.' . $lang->slug . '.', $name) : $lang->slug . '.' . $name;
                    $langData[$lang->slug] = $get($langName);
                }
                $form->fill($langData);
            })
            ->action(function ($data, $set, $get) use ($name) {
                foreach ($data as $key => $value) {
                    $langName = str_contains($name, '.') ? str_replace('.', '.' . $key . '.', $name) : $key . '.' . $name;
                    $set($langName, $value);
                }
            });
    }

    public function getPage(): Component
    {
        $menuSections = MenuSection::query()->pluck('name', 'parent_id')->toArray();
        $menuSections['null'] = 'Without section';
        return Fieldset::make($this->field->label)->schema([
            Radio::make($this->field->name . '.parent_id')->options($menuSections)->required()->live()->columnSpanFull()->inline(),
            Select::make($this->field->name . '.id')->options(function ($get) {
                $parent = $get($this->field->name . '.parent_id') ?? null;
                if ($parent === 'null') {
                    return Page::query()->whereNull('parent_id')->pluck('name', 'id')->toArray();
                }
                return Page::query()->where('parent_id', $get($this->field->name . '.parent_id') ?? null)->pluck('name', 'id')->toArray();
            })->required($this->field->required)->live()->hidden(function ($get) {
                return ! $get($this->field->name . '.parent_id');
            })->columnSpanFull(),
        ]);
    }

    public function getPages(): Component
    {
        $menuSections = MenuSection::query()->pluck('name', 'parent_id')->toArray();
        $menuSections['null'] = 'Without section';
        return Fieldset::make($this->field->label)->schema([
            CheckboxList::make($this->field->name . '.parent_id')->options($menuSections)->required()->live()->columnSpanFull(),
            Grid::make()->columns(3)->schema([
                TextInput::make($this->field->name . '.limit')->numeric()->label(_fields('limit'))->required()->live()->default(5),
                Select::make($this->field->name . '.order_by')->label(_fields('order_by'))->options([
                    'sorting' => 'Default',
                    'name' => 'Name',
                    'random' => 'Random',
                    'updated_at' => 'Date updated',
                ])->required()->native(false)->default('sorting'),
                Toggle::make($this->field->name . '.by_hand')->label(_fields('by_hand'))->default(false)->required()->live()->inline(false),
            ]),
            Select::make($this->field->name . '.ids')->options(function ($get) {
                $page_ids = $get($this->field->name . '.parent_id');
                if (! is_array($page_ids)) {
                    return [];
                }

                return Page::query()->whereIn('parent_id', $page_ids)->pluck('name', 'id')->toArray();
            })
                ->multiple()
                ->required(function ($get) {
                    return $get($this->field->name . '.by_hand');
                })
                ->live()
                ->hidden(function ($get) {
                    return ! $get($this->field->name . '.by_hand');
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

        return Repeater::make($this->field->name)->schema($fields)
            ->cloneable()
            ->default([])
            ->required($this->field->required);
    }
}
