<?php

namespace SmartCms\Core\Services;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
use SmartCms\Core\Models\TemplateSection;

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
            self::PHONE => Select::make($name)->label($label)->options(phones())->required($var['required'] ?? true),
            self::PHONES => Select::make($name)->label($label)->options(phones())->multiple()->required($var['required'] ?? true),
            self::EMAIL => Select::make($name)->label($label)->options(emails())->required($var['required'] ?? true),
            self::ADDRESS => Select::make($name)->label($label)->options(addresses())->required($var['required'] ?? true),
            self::ADDRESSES => Select::make($name)->label($label)->options(addresses())->multiple()->required($var['required'] ?? true),
            self::SCHEDULE => Select::make($name)->label($label)->options(schedules())->required($var['required'] ?? true),
            self::SCHEDULES => Select::make($name)->label($label)->options(schedules())->multiple()->required($var['required'] ?? true),
            self::SOCIALS => Select::make($name)->label($label)->options(social_names())->multiple()->required($var['required'] ?? true),
            self::LINKS => Schema::getSelect($name)->label($label)->options(Menu::query()->pluck('name', 'id')->toArray()),
            self::HEADING => ModuleTitleSchema::run()[0],
            self::DESCRIPTION => ModuleDescriptionSchema::run()[0],
            self::FORM => Select::make($name)->label($label)->options(Form::query()->pluck('name', 'id')->toArray())->required($var['required'] ?? true),
            self::TEXT => TextInput::make($name)->label($label)->required($var['required'] ?? true),
            self::TEXTAREA => Textarea::make($name)->label($label)->rows(3)->required($var['required'] ?? true),
            self::IMAGE => Schema::getImage($name)->label($label)->required($var['required'] ?? true),
            self::FILE => FileUpload::make($name)->label($label)->required($var['required'] ?? true),
            self::DATE => DatePicker::make($name)->label($label)->required($var['required'] ?? true),
            self::HTML => RichEditor::make($name)->label($label)->required($var['required'] ?? true),
            self::NUMBER => TextInput::make($name)->label($label)->numeric()->required($var['required'] ?? true),
            self::BOOLEAN => Toggle::make($name)->label($label)->required($var['required'] ?? true),
            self::SELECT => Select::make($name)->label($label)->options($var['options'] ?? [])->required($var['required'] ?? true),
            // self::PAGES => Select::make($name)->label($label)->options(Page::query()->pluck('name', 'id')->toArray())->multiple()->required($var['required'] ?? true),
            self::PAGE => Select::make($name)->label($label)->options(Page::query()->pluck('name', 'id')->toArray())->required($var['required'] ?? true),
            self::PAGES => $this->getPages($name, $label, $var),
            self::PRODUCT => Select::make($name)->label($label)->options(\SmartCms\Store\Models\Product::query()->pluck('name', 'id')->toArray())->required($var['required'] ?? true),
            self::PRODUCTS => Select::make($name)->label($label)->options(\SmartCms\Store\Models\Product::query()->pluck('name', 'id')->toArray())->required($var['required'] ?? true)->multiple(),
            default => TextInput::make($name)->label($label)->required($var['required'] ?? true),
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
                $fields = array_merge($fields, self::toFilamentField($var, $prefix.$lang->slug.'.', true));
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

    public function getPages(string $name, string $label, array $var)
    {
        return Section::make('')->schema([
            Select::make($name.'.ids')->label($label)
                ->helperText(_hints('pages'))
                ->options(Page::query()->pluck('name', 'id')->toArray())->multiple()->suffixAction(
                    Action::make(_actions('settings'))
                        ->label(_actions('settings'))
                        ->slideOver()
                        ->icon('heroicon-o-cog')
                        ->mountUsing(function ($form) use ($name) {
                            if ($form->model instanceof TemplateSection) {
                                $keys = explode('.', $name);
                                $data = $form->model;
                                foreach ($keys as $key) {
                                    if (isset($data[$key])) {
                                        $data = $data[$key];
                                    } else {
                                        $data = null;
                                        break;
                                    }
                                }
                                $form->fill([
                                    'all_children' => $data['all_children'] ?? false,
                                    'parent' => $data['parent'] ?? null,
                                    'order' => $data['order'] ?? 'created_at',
                                    'limit' => $data['limit'] ?? 5,
                                ]);
                            }
                        })
                        ->form(function ($form) {
                            $pages = Page::query()->pluck('name', 'id')->toArray();

                            return $form->schema([
                                Toggle::make('all_children')->label(_fields('all_children'))->helperText(_hints('all_children'))->live()->afterStateUpdated(function ($set, $get) {
                                    if ($get('all_children')) {
                                        $set('parent', null);
                                    }
                                }),
                                Select::make('parent')->label(_fields('all_childrens_of_page'))->options($pages)->helperText(_hints('all_childrens_of_page'))->live()->afterStateUpdated(function ($set, $get) {
                                    if ($get('parent')) {
                                        $set('all_children', false);
                                    }
                                }),
                                Select::make('order')->label(_fields('order'))->options([
                                    'created_at' => _fields('created_at'),
                                    'popularity' => _fields('popularity'),
                                    'random' => _fields('random'),
                                ])->default('created_at')->required(),
                                TextInput::make('limit')->label(_fields('limit'))->numeric()->default(5),
                            ]);
                        })->action(function (array $data, $set, $get, $component) use ($name) {
                            $set($name.'.ids', []);
                            foreach ($data as $key => $value) {
                                $set($name.'.'.$key, $value);
                            }
                        })
                ),
            Hidden::make($name.'.order'),
            Hidden::make($name.'.limit'),
            Hidden::make($name.'.parent'),
            Hidden::make($name.'.all_children'),
        ]);
    }
}
