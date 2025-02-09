<?php

namespace SmartCms\Core\Admin\Pages\Settings;

use Closure;
use Filament\Actions\Action;
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
use Filament\Pages\Dashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use libphonenumber\PhoneNumberType;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use SmartCms\Core\Models\Language;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Services\Config;
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
                            Repeater::make(sconfig('company_info'))->label(_fields('company_info'))
                                ->schema([
                                    TextInput::make('name')
                                        ->label(_fields('branch_name'))
                                        ->required(),
                                    TextInput::make('address')
                                        ->required(),
                                    TextInput::make('coordinates'),
                                    TextInput::make('city'),
                                    TextInput::make('country'),
                                    TextInput::make('schedule'),
                                    TextInput::make('email')->required(),
                                    Schema::getRepeater('phones')->schema([
                                        PhoneInput::make('value')
                                            ->label(strans('admin.phone_number'))
                                            ->validateFor(type: PhoneNumberType::MOBILE)
                                            ->required(),
                                    ]),
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
            Action::make('change_template')
                ->label(_actions('change_template'))
                ->color('danger')
                ->iconic()
                ->fillForm(function (): array {
                    return [
                        'template' => template(),
                    ];
                })
                ->form(function ($form) {
                    return $form->schema([
                        Select::make('template')
                            ->label(_fields('template'))
                            ->options(function () {
                                $templates = Helper::getTemplates();
                                $templates = array_filter($templates, function ($key) {
                                    return $key != template();
                                }, ARRAY_FILTER_USE_KEY);

                                return $templates;
                            })
                            ->required(),
                    ]);
                })
                ->action(function (array $data) {
                    setting([
                        sconfig('template') => $data['template'],
                    ]);
                    Layout::query()->where('template', '!=', $data['template'])->delete();
                    TemplateSection::query()->where('template', '!=', $data['template'])->delete();
                    $menusections = MenuSection::query()->pluck('parent_id')->toArray();
                    foreach ($menusections as $section) {
                        if (Page::query()->where('parent_id', $section)->count() == 0) {
                            MenuSection::query()->where('id', $section)->delete();
                        }
                    }
                    $config = new Config;
                    $config->initLayouts();
                    $config->initMenuSections();
                    $config->initTranslates();
                    Notification::make()
                        ->title(_actions('setup_success'))
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->icon('heroicon-o-cog'),
            Action::make('update_template')
                ->label(_actions('update_template'))
                ->icon('heroicon-o-arrow-path')
                ->iconic()
                ->action(function () {
                    $config = new Config;
                    $config->initLayouts();
                    $config->initMenuSections();
                    $config->initTranslates();
                    Notification::make()
                        ->title(_actions('setup_success'))
                        ->success()
                        ->send();
                }),
            Action::make('change_theme')
                ->label(_actions('change_theme'))
                ->icon('heroicon-o-paint-brush')
                ->iconic()
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
            Action::make(_actions('update'))
                ->icon('heroicon-m-arrow-up-on-square')
                ->label(_actions('update'))
                ->iconic()
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
            Action::make('save_2')
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->save();
                }),
        ];
    }

    protected function beforeSave()
    {
        if ($this->data[sconfig('template')] != template()) {
            Layout::query()->update(['status' => 0]);
            TemplateSection::query()->update(['status' => 0]);
        }
    }
}
