<?php

namespace SmartCms\Core\Services;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use SmartCms\Core\Actions\Schema\ModuleDescriptionSchema;
use SmartCms\Core\Actions\Schema\ModuleTitleSchema;
use SmartCms\Core\Models\Form;
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

    public function toFilamentField(array $var, string $prefix = ''): array
    {
        $fields = [];
        $name = $var['name'];
        if (isset($var['label'])) {
            $label = $var['label'];
        } else {
            $label = Helper::getLabelFromField($name);
        }
        $defLabel = $label;
        $name = $prefix.main_lang().'.'.$name;
        if ($var['type'] == self::ARRAY->value && isset($var['schema'])) {
            $fields = [];
            foreach ($var['schema'] as $key => $variable) {
                $fields = array_merge($fields, Helper::parseVariable($variable, ''));
            }

            return [Repeater::make($prefix.$var['name'])->label($label)->schema($fields)->default([])];
        }
        $fields[] = match ($this) {
            self::PHONE => Select::make($name)->label($label)->options(phones())->required($var['required'] ?? true),
            self::PHONES => Select::make($name)->label($label)->options(phones())->multiple()->required($var['required'] ?? true),
            self::EMAIL => Select::make($name)->label($label)->options(emails())->required($var['required'] ?? true),
            self::ADDRESS => Select::make($name)->label($label)->options(addresses())->required($var['required'] ?? true),
            self::ADDRESSES => Select::make($name)->label($label)->options(addresses())->multiple()->required($var['required'] ?? true),
            self::SCHEDULE => Select::make($name)->label($label)->options(schedules())->required($var['required'] ?? true),
            self::SCHEDULES => Select::make($name)->label($label)->options(schedules())->multiple()->required($var['required'] ?? true),
            self::SOCIALS => Select::make($name)->label($label)->options(social_names())->multiple()->required($var['required'] ?? true),
            self::LINKS => Schema::getLinkBuilder($name)->label($label)->required($var['required'] ?? true),
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
            self::PAGES => Select::make($name)->label($label)->options(Page::query()->pluck('name', 'id')->toArray())->multiple()->required($var['required'] ?? true),
            self::PAGE => Select::make($name)->label($label)->options(Page::query()->pluck('name', 'id')->toArray())->required($var['required'] ?? true),
            default => TextInput::make($name)->label($label)->required($var['required'] ?? true),
        };
        if (is_multi_lang()) {
            foreach (get_active_languages() as $lang) {
                if ($lang->id == main_lang_id()) {
                    continue;
                }
                $fields = array_merge($fields, self::toFilamentField($var, $prefix.$lang->slug.'.'));
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
}
