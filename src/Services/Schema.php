<?php

namespace SmartCms\Core\Services;

use SmartCms\Core\Admin\Resources\StaticPageResource\RelationManagers\TemplateRelationManager;
use SmartCms\Core\Admin\Resources\TranslateResource\RelationManagers\TranslatableRelationManager;
use SmartCms\Core\Models\Section as ModelsSection;
use SmartCms\Core\Models\Setting;
use SmartCms\Core\Models\StaticPage;
use SmartCms\Core\Models\TemplateSection;
use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\Action as ActionsAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumberType;
use Modules\Category\Models\Category;
use Modules\Contractor\Models\Contractor;
use Modules\Order\Models\Order;
use Modules\Order\Services\PaymentService;
use Modules\Manufacturer\Models\Manufacturer;
use Modules\Promotions\Models\Sticker;
use Modules\Seo\Admin\SeoResource\Pages\SeoRelationManager;
use Modules\Team\Models\Team;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Saade\FilamentAdjacencyList\Forms\Components\AdjacencyList;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Schema
{
    public static function getName(bool $isRequired = true): TextInput
    {
        return TextInput::make('name')
            ->label(__('Name'))
            ->helperText(__('The field will be displayed on the page'))
            ->string()
            ->reactive()
            ->required($isRequired);
    }

    public static function getSlug(?string $table = null, bool $isRequired = true): TextInput
    {
        $slug = TextInput::make('slug')
            ->label(__('Slug'))
            ->string()
            ->readOnly()
            ->required($isRequired)
            ->helperText(__('Slug will be generated automatically from title of any language'))
            ->disabled(!$isRequired)
            ->hintAction(Action::make(__('Clear slug'))
                ->requiresConfirmation()
                ->action(function (Set $set, $state) {
                    $set('slug', null);
                }))->default('');
        if ($table) {
            $slug->unique(table: $table, ignoreRecord: true);
        }

        return $slug;
    }

    public static function getReactiveName(bool $isRequired = true): TextInput
    {
        return TextInput::make('name')
            ->label(__('Name'))
            ->string()
            ->reactive()
            ->required($isRequired)
            ->live(debounce: 1000)
            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state, ?Model $record) {
                $isSlugEdit = true;
                if ($record && $record->slug) {
                    $isSlugEdit = false;
                }
                if ($get('title') == $old) {
                    $set('title', $state);
                }
                if ($get('heading') == $old) {
                    $set('heading', $state);
                }
                if (($get('slug') ?? '') !== Str::slug($old) && $get('slug') !== null) {
                    return;
                }
                if ($get('slug') == null && ! $isSlugEdit) {
                    $isSlugEdit = true;
                }
                if ($isSlugEdit) {
                    $set('slug', Str::slug($state));
                }
            });
    }

    public static function getLogo(): FileUpload
    {
        return FileUpload::make('logo')
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([
                '16:9',
                '4:3',
                '1:1',
            ])
            ->reorderable()
            ->openable()
            ->optimize('webp')
            ->disk('public');
    }

    public static function getImages(): FileUpload
    {
        return FileUpload::make('images')
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([
                '16:9',
                '4:3',
                '1:1',
            ])
            ->reorderable()
            ->openable()
            ->optimize('webp')
            ->disk('public')
            ->multiple(true);
    }

    public static function getImage(string $name = 'image', bool $isMultiple = false): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->imageEditor()
            ->helperText(__('Upload the image of the record'))
            ->imageEditorAspectRatios([
                '16:9',
                '4:3',
                '1:1',
            ])
            ->reorderable()
            ->openable()
            ->optimize('webp')
            ->disk('public')
            // ->acceptedFileTypes(['image/jpg,image/jpeg,image/png,image/gif,image/bmp,image/tiff,image/webp'])
            ->multiple($isMultiple);
    }

    public static function getSource(): TextInput
    {
        return TextInput::make('source')
            ->label(__('Source'))
            ->string()
            ->nullable();
    }

    public static function getSorting(): TextInput
    {
        return TextInput::make('sorting')
            ->label(__('Sorting'))
            ->numeric()->default(0)
            ->helperText(__('The lower the number, the higher the record will be displayed'));
    }

    public static function getStatus(): Toggle
    {
        return Toggle::make('status')
            ->label(__('Status'))
            ->required()
            ->default(Status::ON)
            ->helperText(__('If status is off, the record will not be displayed on the site'));
    }

    public static function getHeading(): TextInput
    {
        return TextInput::make('heading')
            ->label(__('Heading'))
            ->string();
    }

    public static function getTitle(): TextInput
    {
        return TextInput::make('title')
            ->label(__('Title'))
            ->string();
    }

    public static function getDescription(): Textarea
    {
        return Textarea::make('description')
            ->label(__('Description'))
            ->string();
    }

    public static function getKeywords(): TextInput
    {
        return TextInput::make('keywords')
            ->label(__('Keywords'))
            ->string();
    }

    public static function getPrice(bool $flag = true): TextInput
    {
        return TextInput::make('price')
            ->numeric()
            ->required($flag)
            ->live(debounce: 1000)
            ->prefix(app('currency')->code ?? '')
            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                $state = (int) $state * (int) app('currency')->rate;
                if ($state != $get('final_price')) {
                    $set('final_price', $state);
                }
            });
    }

    public static function getSku(): TextInput
    {
        return TextInput::make('sku')
            ->label(__('Sku'))
            ->string();
    }

    public static function getRepeater(string $name = 'value'): Repeater
    {
        return Repeater::make($name)
            ->addActionLabel('Add')
            ->reorderableWithButtons()
            ->collapsible()
            ->cloneable();
    }

    public static function getSelect(string $name = 'select', array $options = []): Select
    {
        return Select::make($name)
            ->options($options)
            ->native(false);
    }

    public static function getEmail(): TextInput
    {
        return TextInput::make('email')
            ->email()
            ->required()
            ->maxLength(255);
    }

    public static function getUserName(): TextInput
    {
        return TextInput::make('username')
            ->label(__('User name'))
            ->required()
            ->string();
    }

    public static function getPhone(): PhoneInput
    {
        return PhoneInput::make('value')
            ->label(__('Phone number'))
            ->validateFor(type: PhoneNumberType::MOBILE)
            ->required();
    }

    public static function getRating(): Select
    {
        $rating = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        ];

        return self::getSelect('rating', $rating)->label(__('Rating'))->required()->default(5);
    }

    public static function getUpdatedAt(): DatePicker
    {
        return DatePicker::make('updated_at')
            ->default(now())
            ->required();
    }

    public static function getPhonesRepeater(string $name = 'value'): Repeater
    {
        return self::getRepeater($name)
            ->schema([
                PhoneInput::make('value')
                    ->label(__('Phone number'))
                    ->validateFor(type: PhoneNumberType::MOBILE)
                    ->required(),
            ])
            ->default([]);
    }

    public static function getSocialMediaRepeater(string $name = 'value'): Repeater
    {
        return self::getRepeater($name)
            ->schema([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->string()
                    ->required(),
                TextInput::make('url')
                    ->label(__('URL'))
                    ->string()
                    ->required(),
                FileUpload::make('icon')
                    ->label(__('Icon'))
                    ->image()
                    ->required(),
            ])
            ->default([]);
    }


    public static function getStickerType(): Select
    {
        return self::getSelect('type', Sticker::getTypes())
            ->label(__('Sticker type'))
            ->required()->live()->reactive();
    }

    public static function getStickers(): Select
    {
        return self::getSelect('stickers', Sticker::query()->pluck('name', 'id')->toArray())
            ->label(__('Stickers'))
            ->multiple();
    }

    public static function getRate(): TextInput
    {
        return TextInput::make('rate')
            ->numeric()
            ->required();
    }

    public static function getToggle(string $name, string $label = ''): Toggle
    {
        if ($label == '') {
            $label = $name;
        }

        return Toggle::make($name)
            ->label($label)
            ->default(false);
    }

    public static function getDateTime(string $name): DatePicker
    {
        return DatePicker::make($name)->required();
    }

    // public static function getProductVariants(): array
    // {
    //     return [self::getRepeater('variants')->schema([
    //         TextInput::make('id')->hidden(),
    //         self::getImage('image'),
    //         self::getSku(),
    //     ])];
    // }


    public static function getLinkBuilder(string $name): AdjacencyList
    {
        $links = StaticPage::query()->pluck('name', 'slug')->toArray();
        if (module_enabled('Category')) {
            $categories = \Modules\Category\Models\Category::query()->pluck('name', 'slug')->toArray();
            foreach ($categories as $key => $category) {
                $links[$key] = $category;
            }
        } else {
            $categories = [];
        }
        $otherSection = [TextInput::make('other.url')
            ->label(__('URL'))
            ->required()];
        // foreach (get_active_languages() as $lang) {
        // $otherSection[] = TextInput::make('other.label')
        //     ->label('Label')
        //     ->required()
        //     ->hint('Translatable')
        //     ->hintIcon('heroicon-m-language')
        //     ->helperText(__('The field will be displayed on the page'));
        // }
        return AdjacencyList::make($name)->columnSpanFull()
            ->maxDepth(1)
            ->labelKey('name')
            ->modal(false)
            ->form([
                // TextInput::make('label')
                //     ->required(),
                // Schema::getSelect('type', Setting::getPageTypes())->live(),
                Schema::getSelect('slug', $links)->live()->afterStateUpdated(function (Get $get, Set $set, $old, $state) {
                    if($state == null){
                        $state = "";
                    }
                    if ($state !== null) {
                        $page = StaticPage::query()->where('slug', $state)->first();
                        if ($page) {
                            $set('name', $page->name);
                            $set('entity_id', $page->id);
                            $set('entity_type', StaticPage::class);
                        }
                        if (module_enabled('Category')) {
                            $category = Category::query()->where('slug', $state)->first();
                            if ($category) {
                                $set('name', $category->name);
                                $set('entity_id', $category->id);
                                $set('entity_type', Category::class);
                            }
                        }
                    } else {
                        $set('name', null);
                        $set('entity_id', null);
                        $set('entity_type', null);
                    }
                }),
                // Schema::getSelect('category', $categories)->hidden(fn(Get $get): bool => $get('type') != Setting::CATEGORY_TYPE),
                // Schema::getSelect('news', $news)->hidden(fn(Get $get): bool => $get('type') != Setting::NEWS_TYPE),
                // Section::make()->schema($otherSection)->hidden(fn(Get $get): bool => $get('type') != Setting::OTHER),
            ]);
    }

    // public static function getButton(): Section
    // {
    //     $tabs = [];
    //     foreach (get_active_languages() as $lang) {
    //         $tabs[] = Tab::make($lang->name)->schema([
    //             TextInput::make('value.button.' . $lang->slug . '.name')->required(),
    //             TextInput::make('value.button.' . $lang->slug . '.link')->required(),
    //         ]);
    //     }
    //     $tabs[] = Tab::make('Button')->schema([
    //         Toggle::make('value.button.is_dark')->label(__('Dark mode'))->default(false),
    //     ]);

    //     return Section::make(__('Button'))->schema([Tabs::make('tabs')->tabs($tabs)]);
    // }

    public static function getOldUrl(): TextInput
    {
        return TextInput::make('old_url')
            ->label(__('Old URL'))
            ->helperText(__('Copy the old URL from the browser address bar. This url will be redirected to the new one'))
            ->required();
    }

    public static function getNewUrl(): TextInput
    {
        return TextInput::make('new_url')
            ->label(__('New URL'))
            ->helperText(__('Enter the new URL. This url will be displayed in the browser address bar after redirecting from the old one'))
            ->required();
    }

    public static function getComment(): Textarea
    {
        return Textarea::make('comment')
            ->label(__('Comment'));
    }

    public static function getFirstName(): TextInput
    {
        return TextInput::make('firstname')
            ->label('First name')
            ->maxLength(255);
    }

    public static function getLastName(): TextInput
    {
        return TextInput::make('lastname')
            ->label('Last name')
            ->maxLength(255);
    }

    public static function getPassword(): TextInput
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->maxLength(255)
            ->required();
    }

    public static function getEmailVerifiedAt(): DateTimePicker
    {
        return DateTimePicker::make('email_verified_at')
            ->label('Email verified at');
    }

    public static function getTemplateBuilder(): Repeater
    {
        $options = TemplateSection::query()->pluck('name', 'id')->toArray();

        return self::getRepeater('template')
            ->hiddenLabel(true)
            ->helperText(__('Select default template sections for the page'))
            ->schema([
                self::getSelect('template_section_id', $options),
                Hidden::make('value')->default([]),
            ])->default([]);
    }

    public static function getModuleTemplateSelect(string $name): Select
    {
        $files = [];
        foreach (File::files(base_path('template/views/' . $name)) as $file) {
            $fileName = $file->getFilename();
            $fileName  = explode('.', $fileName)[0] ?? $file->getFilenameWithoutExtension();
            $files[$name . '.' . $fileName] = $file->getFilenameWithoutExtension();
        }
        return Select::make('design')
            ->label(__('Design'))
            ->options($files)
            ->native(false)
            ->searchable()
            ->helperText(__('Select the design for the page'))
            ->default($name . '.default')
            ->required();
    }

    public static function getComission(): TextInput
    {
        return TextInput::make('commission')
            ->label(__('Commission'))
            ->numeric()
            ->suffix('%')
            ->placeholder(__('Commission'));
    }

    public static function getSubject(): TextInput
    {
        return TextInput::make('subject')
            ->label(__('Subject'))
            ->string()
            ->helperText(__('Subject of the letter.'));
    }

    public static function getAdditional(): Toggle
    {
        return Toggle::make('additional')
            ->label(__('Additional'))
            ->default(false)
            ->reactive()
            ->helperText(__('If the toggle switch is off, the mailing will be sent to all addresses from the subscriber base, and if it is on, you can specify who to send to.'));
    }

    public static function getAddresses(): TextInput
    {
        return TextInput::make('addresses')
            ->label(__('Addresses'))
            ->placeholder('email1@example.com,email2@example.com,email3@example.com...')
            ->string()
            ->helperText(__('Enter email addresses separated by commas.'))
            ->visible(fn(Get $get) => $get('additional'))
            ->required(fn(Get $get) => $get('additional'));
    }

    public static function getMailsTemplate(): Select
    {
        $templates = File::files(module_path('MailSender', 'Resources/views/mails'));
        $templatesArray = [];

        foreach ($templates as $template) {
            $filePath = module_path('MailSender', 'Resources/views/mails');
            $fileName = ucfirst(str_replace('.blade', '', $template->getFilenameWithoutExtension()));
            $templatesArray[$filePath] = $fileName;;
        }

        return self::getSelect('template', $templatesArray)
            ->helperText(__('Select font for website'));
    }

    public static function getText(): Textarea
    {
        return Textarea::make('text')
            ->label(__('Text'))
            ->string();
    }

    public static function getCreatedAt(): DatePicker
    {
        return DatePicker::make('created_at')
            ->label(__('Created At'))
            ->default(now());
    }

    public static function getContractor(string $name = 'select', array $options = []): Select
    {
        return self::getSelect('contractor_id', Contractor::query()->pluck('name', 'id')->toArray());
    }

    public static function getManufacturer(string $name = 'select', array $options = []): Select
    {
        return self::getSelect('manufacturer_id', Manufacturer::query()->pluck('name', 'id')->toArray());
    }

    public static function getSeoAndTemplateRelationGroup(): RelationGroup
    {
        return RelationGroup::make('Seo and template', [
            TranslatableRelationManager::class,
            SeoRelationManager::class,
            TemplateRelationManager::class
        ]);
    }

    public static function helpAction(string $text): ActionsAction
    {
        return ActionsAction::make(__('Help'))
            ->iconButton()
            ->icon('heroicon-o-question-mark-circle')
            ->modalDescription(__($text))
            ->modalFooterActions([]);
    }

    public static function getContacts(): Repeater
    {
        return self::getRepeater('contacts')
            ->schema([
                PhoneInput::make('value')
                    ->label(__('Phone number'))
                    ->validateFor(type: PhoneNumberType::MOBILE),
            ]);
    }

    public static function getSocials(): Repeater
    {
        return self::getRepeater('socials')
            ->schema([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->string()
                    ->required(),
                TextInput::make('url')
                    ->label(__('URL'))
                    ->string()
                    ->required(),
                Schema::getImage('icon')
                    ->label(__('Icon'))
                    ->required(),
            ]);
    }

    public static function getAuthors(): Select
    {
        return Select::make('teams')
            ->label('Authors')
            ->multiple()
            ->relationship('teams', 'name')
            ->options(Team::all()->pluck('name', 'id'));
    }
}
