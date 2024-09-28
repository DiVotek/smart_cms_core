<?php

namespace SmartCms\Core\Admin\Pages\Settings;

use Closure;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use libphonenumber\PhoneNumberType;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use Schmeits\FilamentCharacterCounter\Forms\Components\Textarea;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Services\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Settings extends BaseSettings
{
    public static function getNavigationGroup(): ?string
    {
        return strans('admin.system');
    }

    public static function getNavigationBadge(): ?string
    {
        return 8;
    }

    public function schema(): array|Closure
    {
        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make(strans('admin.general'))->schema([
                        TextInput::make(config('settings.company_name'))->required(),
                        Textarea::make(config('settings.company_description'))->characterLimit(255),
                        Textarea::make(config('settings.company_slogan'))->characterLimit(120),
                        Select::make(config('settings.main_language'))
                            ->options(Language::query()->pluck('name', 'id')->toArray())
                            ->required(),
                    ]),
                    Tabs\Tab::make(strans('admin.branding'))
                        ->schema([
                            Schema::getImage(config('settings.header_logo'))->required(),
                            Schema::getImage(config('settings.footer_logo')),
                            Schema::getImage(config('settings.favicon')),
                            Schema::getRepeater(config('settings.socials'))
                                ->schema([
                                    TextInput::make('name')
                                        ->label(strans('admin.name'))
                                        ->string()
                                        ->required(),
                                    TextInput::make('url')
                                        ->label(strans('admin.url'))
                                        ->string()
                                        ->required(),
                                    Schema::getImage('icon')
                                        ->label(strans('admin.icon'))
                                        ->required(),
                                ])
                                ->default([]),
                        ]),
                    Tabs\Tab::make(strans('admin.company_info'))
                        ->schema([
                            TextInput::make('country'),
                            Repeater::make(config('settings.company_info'))
                                ->schema([
                                    TextInput::make('address')
                                        ->required(),
                                    TextInput::make('coordinates'),
                                    TextInput::make('schedule')->required(),
                                    TextInput::make('email')->required(),
                                    Schema::getRepeater('phones')->schema([
                                        PhoneInput::make('value')
                                            ->label(strans('admin.phone_number'))
                                            ->validateFor(type: PhoneNumberType::MOBILE)
                                            ->required(),
                                    ]),
                                ]),
                        ]),
                    Tabs\Tab::make(strans('admin.mail'))
                        ->schema([
                            TextInput::make('admin_mails')
                                ->label(strans('admin.admin_mails'))->helperText(strans('admin.admin_mails_helper')),
                            Fieldset::make(__('Mailer'))
                                ->schema([
                                    TextInput::make(config('settings.mailer.host'))->label(strans('admin.host')),
                                    TextInput::make(config('settings.mailer.port'))->label(strans('admin.port')),
                                    TextInput::make(config('settings.mailer.username'))->label(strans('admin.username')),
                                    TextInput::make(config('settings.mailer.password'))->label(strans('admin.password')),
                                    Select::make(config('settings.mailer.encryption'))
                                        ->label(strans('admin.encryption'))
                                        ->options([
                                            'ssl' => 'SSL',
                                            'tls' => 'TLS',
                                        ]),
                                    TextInput::make(config('settings.mailer.from'))->label(strans('admin.from')),
                                    TextInput::make(config('settings.mailer.name'))->label(strans('admin.name')),
                                ]),
                        ]),
                ]),
        ];
    }
}
