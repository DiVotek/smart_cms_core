<?php

namespace SmartCms\Core\Admin\Pages\Settings;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard;
use Illuminate\Contracts\Support\Htmlable;
use libphonenumber\PhoneNumberType;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use SmartCms\Core\Actions\InitMenuSections;
use SmartCms\Core\Jobs\UpdateJob;
use SmartCms\Core\Models\Admin;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Seo;
use SmartCms\Core\Notifications\TestNotification;
use SmartCms\Core\Services\AdminNotification;
use SmartCms\Core\Services\Frontend\LayoutService;
use SmartCms\Core\Services\Frontend\SectionService;
use SmartCms\Core\Services\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Settings extends BaseSettings
{
    public static function getNavigationGroup(): ?string
    {
        return _nav('system');
    }

    public static function getNavigationLabel(): string
    {
        return _nav('settings');
    }

    public static function getNavigationBadge(): ?string
    {
        return 8;
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return null;
    }

    public function getBreadcrumbs(): array
    {
        if (config('shared.admin.breadcrumbs', false)) {
            return [
                Dashboard::getUrl() => _nav('dashboard'),
                _nav('settings'),
            ];
        }

        return [];
    }

    public function schema(): array|Closure
    {
        $addressSchema = [];
        foreach (get_active_languages() as $language) {
            $addressSchema[] = Hidden::make($language->slug)
                ->label($language->name);
        }

        return [
            Tabs::make('Settings')
                ->persistTabInQueryString()
                ->id('settings-tabs')
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
                            ->live()
                            ->required()->hidden(function ($get) {
                                return ! $get(sconfig('is_multi_lang'));
                            }),
                        Select::make(sconfig('front_languages'))
                            ->label(_fields('front_languages'))
                            ->options(function ($get) {
                                return Language::query()->whereIn('id', $get(sconfig('additional_languages', [])))->pluck('name', 'id')->toArray();
                            })
                            ->multiple()
                            ->required()->hidden(function ($get) {
                                return ! $get(sconfig('is_multi_lang'));
                            }),
                        Schema::getImage(sconfig('branding.logo'), path: 'branding')
                            ->label(_fields('logo'))
                            ->required(),
                    ]),
                    Tabs\Tab::make(strans('admin.branding'))
                        ->schema([
                            Schema::getImage(name: sconfig('branding.favicon'), path: 'branding')->label(_fields('favicon')),
                            Schema::getRepeater(sconfig('branding.socials'))->label(_fields('socials'))
                                ->schema([
                                    TextInput::make('name')
                                        ->label(strans('admin.name'))
                                        ->string()
                                        ->required(),
                                    TextInput::make('link')
                                        ->label(strans('admin.url'))
                                        ->string()
                                        ->required(),
                                    Schema::getImage('image', path: 'branding')
                                        ->label(strans('admin.icon'))->default(''),
                                ])
                                ->default([]),
                        ]),
                    Tabs\Tab::make(strans('admin.company_info'))
                        ->schema([
                            Repeater::make(sconfig('company_info.phones'))->label(_fields('phones'))
                                ->schema([
                                    PhoneInput::make('value')
                                        ->label(strans('admin.phone_number'))
                                        ->validateFor(type: PhoneNumberType::MOBILE)
                                        ->required(),
                                ]),
                            Repeater::make(sconfig('company_info.emails'))->label(_fields('emails'))
                                ->schema([
                                    TextInput::make('value')
                                        ->email()
                                        ->label(_fields('email'))
                                        ->required(),
                                ]),
                            Repeater::make(sconfig('company_info.addresses'))->label(_fields('addresses'))
                                ->schema([
                                    ...$addressSchema,
                                    TextInput::make('default')
                                        ->label(_fields('branch_name'))
                                        ->required()->suffixAction(ActionsAction::make('translate')->icon('heroicon-o-language')
                                            ->fillForm(function ($get) {
                                                $values = [];
                                                foreach (get_active_languages() as $language) {
                                                    $values[$language->slug] = $get($language->slug);
                                                }

                                                return $values;
                                            })
                                            ->form(function ($form) {
                                                $schema = [];
                                                foreach (get_active_languages() as $language) {
                                                    $schema[] = TextInput::make($language->slug)
                                                        ->label($language->name);
                                                }

                                                return $form->schema($schema);
                                            })->action(function ($data, $set) {
                                                foreach ($data as $key => $value) {
                                                    $set($key, $value);
                                                }
                                            })),
                                ]),
                        ]),
                    Tabs\Tab::make(strans('admin.seo'))->schema([
                        Toggle::make(sconfig('indexation'))
                            ->label(_fields('indexation'))
                            ->helperText(_hints('indexation'))
                            ->required(),
                        TextInput::make(sconfig('gtm'))
                            ->label(_fields('google_tag'))
                            ->helperText(_hints('gtm'))
                            ->string(),
                        Fieldset::make(_fields('title'))
                            ->schema([
                                TextInput::make(sconfig('title.prefix'))
                                    ->label(_fields('prefix'))
                                    ->string()
                                    ->helperText(_hints('title_prefix')),
                                TextInput::make(sconfig('title.suffix'))
                                    ->label(_fields('suffix'))
                                    ->helperText(_hints('title_suffix'))
                                    ->string(),
                            ]),
                        Fieldset::make(_fields('description'))
                            ->schema([
                                TextInput::make(sconfig('description.prefix'))
                                    ->label(_fields('prefix'))
                                    ->string()
                                    ->helperText(_hints('description_prefix')),
                                TextInput::make(sconfig('description.suffix'))
                                    ->label(_fields('suffix'))
                                    ->helperText(_hints('description_suffix'))
                                    ->string(),
                            ]),
                        Schema::getRepeater(sconfig('custom_meta'))
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
                        Schema::getRepeater(sconfig('custom_scripts'))
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
                        TextInput::make('og_type')
                            ->label(_fields('og_type')),
                        Schema::getImage('og_image')
                            ->label(_fields('og_image')),
                    ]),
                    Tabs\Tab::make(strans('admin.images'))->schema([
                        Schema::getImage(sconfig('no_image'))
                            ->label(_fields('no_image')),
                        \Filament\Forms\Components\Section::make(_fields('resize'))->schema([
                            Toggle::make(sconfig('resize.enabled'))
                                ->label(_fields('resize_enabled'))
                                ->formatStateUsing(function ($state) {
                                    return $state ?? true;
                                })->live(),
                            Toggle::make(sconfig('resize.two_sides'))
                                ->label(_fields('resize_two_sides'))
                                ->formatStateUsing(function ($state) {
                                    return $state ?? true;
                                })->hidden(function ($get) {
                                    return ! $get(sconfig('resize.enabled'));
                                }),
                            Toggle::make(sconfig('resize.autoscale'))
                                ->label(_fields('resize_autoscale'))
                                ->formatStateUsing(function ($state) {
                                    return $state ?? false;
                                })->hidden(function ($get) {
                                    return ! $get(sconfig('resize.enabled'));
                                }),
                        ]),
                    ]),
                    Tabs\Tab::make(strans('admin.notifications'))->schema([
                        \Filament\Forms\Components\Section::make(_fields('email'))
                            ->headerActions([
                                Actions\Action::make('test_notification')
                                    ->label(_fields('test_notification'))
                                    ->icon('heroicon-o-envelope')
                                    ->action(function () {
                                        try {
                                            Admin::query()->find(auth()->user()->id)->notifyNow(new TestNotification('mail'));
                                            Notification::make()
                                                ->title(_actions('test_notification_sent'))
                                                ->success()
                                                ->send();
                                        } catch (Exception $e) {
                                            Notification::make()
                                                ->title(_actions('test_notification_error'))
                                                ->body($e->getMessage())
                                                ->danger()
                                                ->send();
                                        }
                                    }),
                            ])
                            ->schema([
                                TextInput::make(sconfig('mail.from'))
                                    ->label(_fields('mail_from_email')),
                                TextInput::make(sconfig('mail.name'))
                                    ->label(_fields('mail_from_name')),
                                Select::make(sconfig('mail.provider'))
                                    ->label(_fields('mail_provider'))
                                    ->options([
                                        'smtp' => 'SMTP',
                                        'sendmail' => 'Sendmail',
                                    ])
                                    ->formatStateUsing(function ($state) {
                                        return $state ?? 'sendmail';
                                    })
                                    ->live()
                                    ->default('sendmail'),
                                TextInput::make(sconfig('mail.host'))
                                    ->label(_fields('host'))
                                    ->hidden(function ($get) {
                                        return $get(sconfig('mail.provider')) != 'smtp';
                                    })->required(),
                                TextInput::make(sconfig('mail.port'))
                                    ->label(_fields('port'))
                                    ->hidden(function ($get) {
                                        return $get(sconfig('mail.provider')) != 'smtp';
                                    })->required(),
                                TextInput::make(sconfig('mail.username'))
                                    ->label(_fields('username'))
                                    ->hidden(function ($get) {
                                        return $get(sconfig('mail.provider')) != 'smtp';
                                    })->required(),
                                TextInput::make(sconfig('mail.password'))
                                    ->label(_fields('password'))
                                    ->password()
                                    ->revealable(false)
                                    ->hidden(function ($get) {
                                        return $get(sconfig('mail.provider')) != 'smtp';
                                    })->required(),
                                Select::make(sconfig('mail.encryption'))
                                    ->label(_fields('encryption'))
                                    ->options([
                                        'ssl' => 'SSL',
                                        'tls' => 'TLS',
                                    ])
                                    ->hidden(function ($get) {
                                        return $get(sconfig('mail.provider')) != 'smtp';
                                    })->required(),
                            ])->collapsible(),
                        \Filament\Forms\Components\Section::make(_fields('telegram'))->schema([
                            TextInput::make(sconfig('telegram.token'))
                                ->label(_fields('bot_token'))
                                ->password()
                                ->revealable(false)->readOnly(fn($get, $state) => ! $get('is_token_deleted') || $state)->suffixAction(ActionsAction::make('delete_token')->icon('heroicon-o-trash')->action(function ($set) {
                                    $set('is_token_deleted', true);
                                    $set(sconfig('telegram.token'), null);
                                })),
                            TextInput::make(sconfig('telegram.bot_username'))
                                ->label(_fields('bot_username'))
                                ->required(),
                        ])->collapsible()->headerActions([
                            Actions\Action::make('test_notification')
                                ->label(_fields('test_notification'))
                                ->icon('heroicon-o-envelope')
                                ->action(function () {
                                    $user = auth()->user();
                                    if (! $user->telegram_id) {
                                        Notification::make()
                                            ->title(_fields('you_dont_have_telegram_id'))
                                            ->danger()
                                            ->send();

                                        return;
                                    }
                                    try {
                                        AdminNotification::make()->title(_fields('test_notification'))->success()->send($user, new TestNotification('telegram'));
                                        Notification::make()
                                            ->title(_fields('test_notification_sent'))
                                            ->success()
                                            ->send();
                                    } catch (Exception $e) {
                                        Notification::make()
                                            ->title(_actions('test_notification_error'))
                                            ->body($e->getMessage())
                                            ->danger()
                                            ->send();
                                    }
                                })->disabled(function ($get) {
                                    return ! $get(sconfig('telegram.token')) || ! $get(sconfig('telegram.bot_username'));
                                }),
                        ]),
                    ]),
                    Tabs\Tab::make(strans('admin.system'))->schema([
                        Toggle::make(sconfig('system.maintenance'))
                            ->label(_fields('maintenance'))
                            ->helperText(_hints('maintenance'))
                            ->formatStateUsing(function ($state) {
                                return $state ?? false;
                            })
                            ->required(),
                        Toggle::make(sconfig('system.debug'))
                            ->label(_fields('debug'))
                            ->helperText(_hints('debug'))
                            ->formatStateUsing(function ($state) {
                                return $state ?? true;
                            })
                            ->required(),
                        Toggle::make(sconfig('system.spa_mode'))
                            ->label(_fields('spa_mode'))
                            ->helperText(_hints('spa_mode'))
                            ->formatStateUsing(function ($state) {
                                return $state ?? true;
                            })
                            ->required(),
                    ]),
                ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('download_logs')
                    ->label(_actions('download_logs'))
                    ->icon('heroicon-m-arrow-down-tray')
                    ->action(function () {
                        return response()->download(storage_path('logs/laravel.log'));
                    }),

                Action::make('clear_logs')
                    ->label(_actions('clear_logs'))
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function () {
                        file_put_contents(storage_path('logs/laravel.log'), '');
                        Notification::make()
                            ->title(_actions('cleared_logs'))
                            ->success()
                            ->send();
                    }),
            ]),
            Action::make('update_template')
                ->tooltip(_actions('update_template'))
                ->label(_actions('update_template'))
                ->icon('heroicon-o-arrow-path')
                ->iconic()
                ->action(function () {
                    SectionService::make()->init();
                    LayoutService::make()->init();
                    InitMenuSections::run();
                    Notification::make()
                        ->title(_actions('setup_success'))
                        ->success()
                        ->send();
                }),
            Action::make('change_theme')
                ->label(_actions('change_theme'))
                ->tooltip(_actions('change_theme'))
                ->icon('heroicon-o-paint-brush')
                ->iconic()
                ->fillForm(function (): array {
                    $theme = _settings('theme', []);
                    $theme = array_merge($theme, config('theme', []));

                    return [
                        'theme' => $theme,
                    ];
                })
                ->form(function ($form) {
                    $theme = _settings('theme', []);
                    $theme = array_merge(config('theme', []), $theme);
                    $schema = [];
                    foreach ($theme as $key => $value) {
                        $schema[] = ColorPicker::make('theme.' . $key)
                            ->label(ucfirst($key))
                            ->default($value);
                    }

                    return $form->schema($schema);
                })->action(function (array $data): void {
                    if (! isset($data['theme'])) {
                        $data['theme'] = [];
                    }
                    setting([
                        sconfig('theme') => $data['theme'],
                    ]);
                }),
            Action::make(_actions('update'))
                ->icon('heroicon-m-arrow-up-on-square')
                ->tooltip(_actions('update'))
                ->label(_actions('update'))
                ->disabled(function () {
                    return (float) \Composer\InstalledVersions::getPrettyVersion('smart-cms/core') <= (float) _settings('version', 0);
                })
                ->iconic()
                ->requiresConfirmation()->action(function () {
                    UpdateJob::dispatch();
                    Notification::make()
                        ->title(_actions('update_started'))
                        ->success()
                        ->send();
                }),
            Action::make('save_2')
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->save();
                }),
            Action::make('cancel')
                ->color('gray')
                ->label(_actions('cancel'))
                ->url(fn() => self::getUrl()),
        ];
    }

    public function beforeSave()
    {
        $state = $this->form->getState();
        $mainLang = $state[sconfig('main_language')];
        if ($mainLang != main_lang_id() && ! is_multi_lang()) {
            Seo::query()->where('language_id', main_lang_id())->update([
                'language_id' => $mainLang,
            ]);
        }
    }
}
