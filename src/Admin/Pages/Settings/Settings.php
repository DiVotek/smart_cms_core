<?php

namespace SmartCms\Core\Admin\Pages\Settings;

use Closure;
use Filament\Actions\Action;
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
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Seo;
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
                ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
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
                        $schema[] = ColorPicker::make('theme.'.$key)
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
                ->url(fn () => self::getUrl()),
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
