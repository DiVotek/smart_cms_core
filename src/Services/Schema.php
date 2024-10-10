<?php

namespace SmartCms\Core\Services;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\Action as ActionsAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Saade\FilamentAdjacencyList\Forms\Components\AdjacencyList;
use SmartCms\Core\Admin\Resources\SeoResource\Pages\SeoRelationManager;
use SmartCms\Core\Admin\Resources\StaticPageResource\RelationManagers\TemplateRelationManager;
use SmartCms\Core\Admin\Resources\TranslateResource\RelationManagers\TranslatableRelationManager;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\TemplateSection;

class Schema
{
    public static function getName(bool $isRequired = true): TextInput
    {
        return TextInput::make('name')
            ->label(_fields('name'))
            ->helperText(__('The field will be displayed on the page'))
            ->string()
            ->reactive()
            ->required($isRequired);
    }

    public static function getSlug(?string $table = null, bool $isRequired = true): TextInput
    {
        $slug = TextInput::make('slug')
            ->label(_fields('slug'))
            ->string()
            ->readOnly()
            ->required($isRequired)
            ->helperText(__('Slug will be generated automatically from title of any language'))
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
            ->label(_fields('name'))
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
            ->multiple($isMultiple);
    }

    public static function getSorting(): TextInput
    {
        return TextInput::make('sorting')
            ->label(_fields('sorting'))
            ->numeric()->default(1)
            ->helperText(__('The lower the number, the higher the record will be displayed'));
    }

    public static function getStatus(): Toggle
    {
        return Toggle::make('status')
            ->label(_fields('status'))
            ->required()
            ->default(Status::ON)
            ->helperText(__('If status is off, the record will not be displayed on the site'));
    }

    public static function getRepeater(string $name = 'value'): Repeater
    {
        return Repeater::make($name)
            ->addActionLabel(_fields('repeater_add'))
            ->reorderableWithButtons()
            ->collapsible()
            ->cloneable();
    }

    public static function getSelect(string $name = 'select', array $options = []): Select
    {
        return Select::make($name)
            ->options($options)
            ->native(false)->searchable(true);
    }

    public static function getUpdatedAt(): DatePicker
    {
        return DatePicker::make('updated_at')
            ->label(_fields('updated_at'))
            ->default(now())
            ->required();
    }

    public static function getLinkBuilder(string $name): AdjacencyList
    {
        $links = Page::query()->pluck('name', 'slug')->toArray();

        return AdjacencyList::make($name)->columnSpanFull()
            ->maxDepth(1)
            ->labelKey('name')
            ->modal(false)
            ->form([
                Schema::getSelect('slug', $links)->live()->afterStateUpdated(function (Get $get, Set $set, $old, $state) {
                    if ($state == null) {
                        $state = '';
                    }
                    if ($state !== null) {
                        $page = Page::query()->where('slug', $state)->first();
                        if ($page) {
                            $set('name', $page->name);
                            $set('entity_id', $page->id);
                            $set('entity_type', Page::class);
                        }
                    } else {
                        $set('name', null);
                        $set('entity_id', null);
                        $set('entity_type', null);
                    }
                }),
            ]);
    }

    public static function getTemplateBuilder(string $name = 'template'): Repeater
    {
        $options = TemplateSection::query()->pluck('name', 'id')->toArray();

        return self::getRepeater($name)
            ->hiddenLabel(true)
            ->helperText(_hints('template'))
            ->schema([
                self::getSelect('template_section_id', $options),
                Hidden::make('value')->default([]),
            ])->default([]);
    }

    public static function getSeoAndTemplateRelationGroup(): RelationGroup
    {
        return RelationGroup::make(_fields('seo_and_template'), [
            TranslatableRelationManager::class,
            SeoRelationManager::class,
            TemplateRelationManager::class,
        ]);
    }

    public static function helpAction(string $text): ActionsAction
    {
        return ActionsAction::make(_hints('help'))
            ->iconButton()
            ->icon('heroicon-o-question-mark-circle')
            ->modalDescription(__($text))
            ->modalFooterActions([]);
    }
}
