<?php

namespace SmartCms\Core\Services;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use SmartCms\Core\Actions\Schema\ModuleDescriptionSchema;
use SmartCms\Core\Actions\Schema\ModuleTitleSchema;
use SmartCms\Core\Models\Form;
use SmartCms\Core\Models\Menu;
use SmartCms\Core\Models\Page;

enum VariableTypes: string
{
    case PHONE = 'phone';
    case PHONES = 'phones';
    case EMAIL = 'email';
    case ADDRESS = 'address';
    case ADDRESSES = 'addresses';
    case SCHEDULE = 'schedule';
    case SCHEDULES = 'schedules';
    case SOCIALS = 'socials';
    case HEADING = 'heading';
    case DESCRIPTION = 'description';
    case LINKS = 'links';
    case FORM = 'form';
    case PAGES = 'pages';
    case PAGE = 'page';

    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case IMAGE = 'image';
    case FILE = 'file';
    case DATE = 'date';
    case HTML = 'html';
    case NUMBER = 'number';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';
    case SELECT = 'select';
    case PRODUCT = 'product';
    case PRODUCTS = 'products';

    public function toFilamentField(array $var, string $prefix = '', $is_lang = false): array
    {
        $fields = [];
        $name = $var['name'];
        if (isset($var['label'])) {
            $label = $var['label'];
        } else {
            $label = Helper::getLabelFromField($name);
        }
        if ($is_lang) {
            $name = $prefix.$name;
        } else {
            $name = $prefix.main_lang().'.'.$name;
        }
        if ($var['type'] == self::ARRAY->value && isset($var['schema'])) {
            $fields = [];
            foreach ($var['schema'] as $key => $variable) {
                $fields = array_merge($fields, Helper::parseVariable($variable, ''));
            }

            return [Repeater::make($prefix.$var['name'])->label($label)->schema($fields)->default([])->cloneable()];
        }
        // if ($var['type'] == self::PAGES->value) {
        //     $pages = Page::query()->pluck('name', 'id')->toArray();

        //     $fields[] = ;

        //     // return $fields;
        // }
        $fields[] = match ($this) {
            self::PHONE => $this->getPhones($name, $label, $var, false),
            self::PHONES => $this->getPhones($name, $label, $var),
            self::EMAIL => $this->getEmail($name, $label, $var),
            self::ADDRESS => $this->getAddresses($name, $label, $var, false),
            self::ADDRESSES => $this->getAddresses($name, $label, $var),
            self::SCHEDULE => $this->getSchedules($name, $label, $var, false),
            self::SCHEDULES => $this->getSchedules($name, $label, $var),
            self::SOCIALS => $this->getSocials($name, $label, $var),
            self::LINKS => $this->getLinks($name, $label, $var),
            self::HEADING => ModuleTitleSchema::run()[0],
            self::DESCRIPTION => ModuleDescriptionSchema::run()[0],
            self::FORM => $this->getForm($name, $label, $var),
            self::TEXT => $this->getText($name, $label, $var),
            self::TEXTAREA => $this->getTextarea($name, $label, $var),
            self::IMAGE => $this->getImage($name, $label, $var),
            self::FILE => FileUpload::make($name)->label($label)->required($var['required'] ?? true), //todo
            self::DATE => DatePicker::make($name)->label($label)->required($var['required'] ?? true), //todo
            self::HTML => $this->getHtml($name, $label, $var),
            self::NUMBER => TextInput::make($name)->label($label)->numeric()->required($var['required'] ?? true),
            self::BOOLEAN => Toggle::make($name)->label($label)->required($var['required'] ?? true),
            self::SELECT => Select::make($name)->label($label)->options($var['options'] ?? [])->required($var['required'] ?? true),
            self::PAGE => $this->getPages($name, $label, $var, false),
            self::PAGES => $this->getPages($name, $label, $var),
            self::PRODUCT => $this->getProducts($name, $label, $var, false),
            self::PRODUCTS => $this->getProducts($name, $label, $var),
            default => $this->getText($name, $label, $var),
        };
        if ($var['type'] == self::HEADING->value || $var['type'] == self::DESCRIPTION->value) {
            return $fields;
        }
        if (is_multi_lang() && ! $is_lang) {
            foreach (get_active_languages() as $lang) {
                if ($lang->id == main_lang_id()) {
                    continue;
                }
                $var['label'] = $label.' '.$lang->name;
                $fields = array_merge($fields, [Hidden::make($prefix.$lang->slug.'.'.$var['name'])]);
                // $fields = array_merge($fields, self::toFilamentField($var, $prefix . $lang->slug . '.', true));
            }
        }

        return $fields;
    }

    public static function fromType(string $value): static
    {
        if (! in_array($value, array_map(function ($item) {
            return $item->value;
        }, self::cases()))) {
            $value = self::TEXT->value;
        }

        return self::from($value);
    }

    public function getPages(string $name, string $label, array $var, $is_multiple = true)
    {
        if ($is_multiple) {
            return Section::make('')->schema([
                Toggle::make($name.'.auto')->label($label)
                    ->helperText(_hints('pages.auto'))
                    ->live()->afterStateUpdated(function ($set, $get) {}),
                Select::make($name.'.ids')->label($label)
                    ->hidden(function ($get) use ($name) {
                        return $get($name.'.auto') == true;
                    })
                    ->helperText(_hints('pages'))
                    ->options(Page::query()->pluck('name', 'id')->toArray())->multiple()->required($var['required'] ?? true),
                Toggle::make($name.'.all_children')->label(_fields('all_children'))->hidden(function ($get) use ($name) {
                    return $get($name.'.auto') == false;
                })->helperText(_hints('pages.all_children')),
                Select::make($name.'.parent_id')->label(_fields('parent_id'))->hidden(function ($get) use ($name) {
                    return $get($name.'.auto') == false || $get($name.'.all_children') == true;
                })->native(false)->selectablePlaceholder(false)
                    ->options(Page::query()->pluck('name', 'id')->toArray())->required($var['required'] ?? true),
                Group::make([
                    Select::make($name.'.order')->label(_fields('order'))->options([
                        'default' => _fields('default'),
                        'created_at' => _fields('created_at'),
                        'popularity' => _fields('popularity'),
                        'random' => _fields('random'),
                    ])->default('default')->required()->native(false)->selectablePlaceholder(false),
                    TextInput::make($name.'.limit')->label(_fields('limit'))->numeric()->required($var['required'] ?? true)->default(5),
                ])->columns(2),
            ]);
        } else {
            $fields = [];
            foreach ($this->getLanguages() as $lang) {
                $fields[] = Select::make($lang->slug)->label($lang->name)
                    ->options(Page::query()->pluck('name', 'id')->toArray())
                    ->required($var['required'] ?? true);
            }

            return Select::make($name)->label($label)
                ->options(Page::query()->pluck('name', 'id')->toArray())
                ->required($var['required'] ?? true)->suffixAction(
                    $this->getTranslateAction($fields, $name),
                );
        }
    }

    public function getLanguages()
    {
        return get_active_languages()->where('id', '!=', main_lang_id());
    }

    public function getTranslateAction(array $schema, string $name): Action
    {
        return Action::make(_actions('translate'))
            ->icon('heroicon-m-language')
            ->form($schema)
            ->mountUsing(function ($form, $get) use ($name) {
                $langData = [];
                foreach (get_active_languages() as $lang) {
                    if ($lang->id == main_lang_id()) {
                        continue;
                    }
                    $langName = str_replace(main_lang(), $lang->slug, $name);
                    $langData[$lang->slug] = $get($langName);
                }
                $form->fill($langData);
            })
            ->action(function ($data, $set) use ($name) {
                foreach ($data as $key => $value) {
                    $newName = str_replace(main_lang(), $key, $name);
                    $set($newName, $value);
                }
            });
    }

    public function getPhones(string $name, string $label, array $var, $is_multiple = true)
    {
        $fields = [];
        foreach ($this->getLanguages() as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options(phones())
                ->multiple($is_multiple)
                ->required($var['required'] ?? true);
        }

        return Select::make($name)->label($label)
            ->options(phones())
            ->required($var['required'] ?? true)
            ->multiple($is_multiple)
            ->suffixAction(
                $this->getTranslateAction($fields, $name),
            );
    }

    public function getText(string $name, string $label, array $var)
    {
        $fields = [];
        foreach (get_active_languages() as $lang) {
            if ($lang->id == main_lang_id()) {
                continue;
            }
            $fields[] = TextInput::make($lang->slug)->label($lang->name)
                ->required($var['required'] ?? true);
        }

        return TextInput::make($name)->label($label)
            ->required($var['required'] ?? true)
            ->suffixAction(
                $this->getTranslateAction($fields, $name)
            );
    }

    public function getLinks(string $name, string $label, array $var)
    {
        return Schema::getSelect($name)->label($label)->options(Menu::query()->pluck('name', 'id')->toArray());
    }

    public function getForm(string $name, string $label, array $var)
    {
        $fields = [];
        foreach ($this->getLanguages() as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options(Form::query()->pluck('name', 'id')->toArray())
                ->required($var['required'] ?? true);
        }

        return Select::make($name)->label($label)->options(Form::query()->pluck('name', 'id')->toArray())->required($var['required'] ?? true)->suffixAction(
            $this->getTranslateAction($fields, $name),
        );
    }

    public function getEmail(string $name, string $label, array $var)
    {
        $fields = [];
        foreach ($this->getLanguages() as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)->options(emails())->required($var['required'] ?? true);
        }

        return Select::make($name)->label($label)->options(emails())->required($var['required'] ?? true)
            ->suffixAction(
                $this->getTranslateAction($fields, $name)
            );
    }

    public function getAddresses(string $name, string $label, array $var, $is_multiple = true)
    {
        $fields = [];
        foreach ($this->getLanguages() as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options(addresses())
                ->multiple($is_multiple)
                ->required($var['required'] ?? true);
        }

        return Select::make($name)->label($label)
            ->options(addresses())
            ->required($var['required'] ?? true)
            ->multiple($is_multiple)
            ->suffixAction(
                $this->getTranslateAction($fields, $name),
            );
    }

    public function getSchedules(string $name, string $label, array $var, $is_multiple = true)
    {
        $fields = [];
        foreach ($this->getLanguages() as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options(schedules())
                ->multiple($is_multiple)
                ->required($var['required'] ?? true);
        }

        return Select::make($name)->label($label)
            ->options(schedules())
            ->required($var['required'] ?? true)
            ->multiple($is_multiple)
            ->suffixAction(
                $this->getTranslateAction($fields, $name),
            );
    }

    public function getSocials(string $name, string $label, array $var)
    {
        return Select::make($name)->label($label)->options(social_names())->multiple()->required($var['required'] ?? true);
    }

    public function getTextarea(string $name, string $label, array $var)
    {
        $fields = [];
        foreach ($this->getLanguages() as $lang) {
            $fields[] = Textarea::make($lang->slug)->label($lang->name)
                ->required($var['required'] ?? true);
        }

        return Textarea::make($name)->label($label)->rows(3)->required($var['required'] ?? true)->hintAction($this->getTranslateAction($fields, $name));
    }

    public function getImage(string $name, string $label, array $var)
    {
        $fields = [];
        foreach ($this->getLanguages() as $lang) {
            $fields[] = Schema::getImage($lang->slug)->label($lang->name)->required($var['required'] ?? true);
        }

        return Schema::getImage($name)->label($label)->required($var['required'] ?? true)->hintAction($this->getTranslateAction($fields, $name));
    }

    public function getHtml(string $name, string $label, array $var)
    {
        $fields = [];
        foreach ($this->getLanguages() as $lang) {
            $fields[] = RichEditor::make($lang->slug)->label($lang->name)
                ->required($var['required'] ?? true);
        }

        return RichEditor::make($name)->label($label)->required($var['required'] ?? true)->hintAction($this->getTranslateAction($fields, $name));
    }

    public function getProducts(string $name, string $label, array $var, $is_multiple = true)
    {
        $fields = [];
        foreach ($this->getLanguages() as $lang) {
            $fields[] = Select::make($lang->slug)->label($lang->name)
                ->options(\SmartCms\Store\Models\Product::query()->pluck('name', 'id')->toArray())
                ->preload()
                ->multiple($is_multiple)
                ->required($var['required'] ?? true);
        }

        return Select::make($name)->label($label)
            ->options(\SmartCms\Store\Models\Product::query()->pluck('name', 'id')->toArray())
            ->required($var['required'] ?? true)
            ->multiple($is_multiple)
            ->preload()
            ->suffixAction(
                $this->getTranslateAction($fields, $name),
            );
    }
}
