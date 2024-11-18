<?php

namespace SmartCms\Core\Admin\Pages\Settings;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use libphonenumber\PhoneNumberType;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Services\Helper;
use SmartCms\Core\Services\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Settings extends BaseSettings
{
    public static function getNavigationGroup(): ?string
    {
        return _nav('system');
    }

    public static function getNavigationBadge(): ?string
    {
        return 8;
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return null;
    }

    public function schema(): array|Closure
    {
        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make(strans('admin.general'))->schema([
                        TextInput::make(sconfig('company_name'))
                            ->label(_fields('company_name'))
                            ->required(),
                        Select::make(sconfig('main_language'))
                            ->label(_fields('main_language'))
                            ->options(Language::query()->pluck('name', 'id')->toArray())
                            ->required(),
                        Toggle::make(sconfig('is_multi_lang'))
                            ->label(_fields('is_multi_lang'))
                            ->required()->live(),
                        Select::make(sconfig('additional_languages'))
                            ->label(_fields('additional_languages'))
                            ->options(Language::query()->pluck('name', 'id')->toArray())
                            ->multiple()
                            ->required()->hidden(function ($get) {
                                return ! $get(sconfig('is_multi_lang'));
                            }),
                        TextInput::make(sconfig('country'))->label(_fields('country'))->required(),
                        Select::make(sconfig('template'))
                            ->label(_fields('template'))
                            ->suffixAction(
                                ActionsAction::make(_actions('theme'))
                                    ->label(_actions('theme'))
                                    ->slideOver()
                                    ->icon('heroicon-o-cog')
                                    ->fillForm(function (): array {
                                        $theme = _settings('theme', []);
                                        if (empty($theme)) {
                                            $theme = _config()->getTheme();
                                            setting([
                                                sconfig('theme') => _config()->getTheme(),
                                            ]);
                                        }

                                        return [
                                            'theme' => _settings('theme', []),
                                        ];
                                    })
                                    ->action(function (array $data): void {
                                        setting([
                                            sconfig('theme') => $data['theme'],
                                        ]);
                                    })
                                    ->hidden(function () {
                                        return empty(_config()->getTheme());
                                    })
                                    ->form(function ($form) {
                                        $theme = _config()->getTheme();
                                        foreach ($theme as $key => $value) {
                                            $schema[] = ColorPicker::make('theme.'.$key)
                                                ->label(ucfirst($key))
                                                ->default($value);
                                        }

                                        return $form
                                            ->schema([
                                                Section::make('')->schema($schema),
                                            ]);
                                    }),
                            )
                            ->options(Helper::getTemplates())
                            ->native(false)
                            ->searchable(),
                    ]),
                    Tabs\Tab::make(strans('admin.branding'))
                        ->schema([
                            Schema::getImage(sconfig('branding.logo'),path:'branding')
                                ->label(_fields('logo'))
                                ->required(),
                            // Schema::getImage(sconfig('footer_logo'))->label(_fields('footer_logo')),
                            Schema::getImage(name: sconfig('favicon'),path:'branding')->label(_fields('favicon')),
                            Schema::getRepeater(sconfig('socials'))->label(_fields('socials'))
                                ->schema([
                                    TextInput::make('name')
                                        ->label(strans('admin.name'))
                                        ->string()
                                        ->required(),
                                    TextInput::make('url')
                                        ->label(strans('admin.url'))
                                        ->string()
                                        ->required(),
                                    Schema::getImage('icon',path:'branding')
                                        ->label(strans('admin.icon'))->default(''),
                                ])
                                ->default([]),
                        ]),
                    Tabs\Tab::make(strans('admin.company_info'))
                        ->schema([
                            Repeater::make(sconfig('company_info'))->label(_fields('company_info'))
                                ->schema([
                                    TextInput::make('name')
                                        ->label(_fields('branch_name'))
                                        ->required(),
                                    TextInput::make('address')
                                        ->required(),
                                    TextInput::make('coordinates'),
                                    TextInput::make('city'),
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
                                    TextInput::make(sconfig('mail.host'))->label(strans('admin.host')),
                                    TextInput::make(sconfig('mail.port'))->label(strans('admin.port')),
                                    TextInput::make(sconfig('mail.username'))->label(strans('admin.username')),
                                    TextInput::make(sconfig('mail.password'))->label(strans('admin.password')),
                                    Select::make(sconfig('mail.encryption'))
                                        ->label(strans('admin.encryption'))
                                        ->options([
                                            'ssl' => 'SSL',
                                            'tls' => 'TLS',
                                        ]),
                                    TextInput::make(sconfig('mail.from'))->label(strans('admin.from')),
                                    TextInput::make(sconfig('mail.name'))->label(strans('admin.name')),
                                ]),
                        ]),
                    Tabs\Tab::make(strans('admin.seo'))->schema([
                        Toggle::make('indexation')
                            ->label(_fields('indexation'))
                            ->helperText(_hints('indexation'))
                            ->required(),
                        TextInput::make('gtm')
                            ->label(_fields('google_tag'))
                            ->helperText(_hints('gtm'))
                            ->string(),
                        Fieldset::make(_fields('title'))
                            ->schema([
                                TextInput::make('title.prefix')
                                    ->label(_fields('prefix'))
                                    ->string()
                                    ->helperText(_hints('title_prefix')),
                                TextInput::make('title.suffix')
                                    ->label(_fields('suffix'))
                                    ->helperText(_hints('title_suffix'))
                                    ->string(),
                            ]),
                        Fieldset::make(_fields('description'))
                            ->schema([
                                TextInput::make('description.prefix')
                                    ->label(_fields('prefix'))
                                    ->string()
                                    ->helperText(_hints('description_prefix')),
                                TextInput::make('description.suffix')
                                    ->label(_fields('suffix'))
                                    ->helperText(_hints('description_suffix'))
                                    ->string(),
                            ]),
                        Schema::getRepeater('meta')
                            ->label(_fields('custom_meta'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(_fields('name'))
                                    ->string(),
                                TextInput::make('description')
                                    ->label(_fields('description'))
                                    ->string(),
                                Textarea::make('meta_tags')
                                    ->label(_fields('meta_tags')),
                            ])
                            ->default([]),
                        Schema::getRepeater('scripts')
                            ->label(_fields('custom_scripts'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(_fields('name'))
                                    ->string(),
                                TextInput::make('description')
                                    ->label(_fields('description'))
                                    ->string(),
                                Textarea::make('scripts')
                                    ->label(_fields('scripts')),
                            ])
                            ->default([]),
                    ]),
                ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make(_actions('update'))
                ->icon('heroicon-m-arrow-up-on-square')
                ->label(_actions('update'))
                ->requiresConfirmation()->action(function () {
                    $res = Artisan::call('scms:update');
                    if ($res == 0) {
                        Notification::make()
                            ->title(_actions('success_update'))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title(_actions('error_update'))
                            ->error()
                            ->send();
                    }
                }),
            Action::make(_actions('clear_cache'))
                ->icon('heroicon-m-arrow-path')
                ->label(_actions('clear_cache'))
                ->requiresConfirmation()->action(function () {
                    $command = 'optimize';
                    if (config('app.env') != 'production') {
                        $command = 'optimize:clear';
                    }
                    $res = Artisan::call($command);
                    if ($res == 0) {
                        Notification::make()
                            ->title(_actions('clear_cache_success'))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title(_actions('clear_cache_error'))
                            ->error()
                            ->send();
                    }
                }),
        ];
    }
}
