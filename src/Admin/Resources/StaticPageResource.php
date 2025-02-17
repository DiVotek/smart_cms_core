<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages as Pages;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Translate;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder as SchemaBuilder;
use SmartCms\Core\Services\TableSchema;

class StaticPageResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return _nav('pages');
    }

    public static function getModel(): string
    {
        return config('shared.page_model', Page::class);
    }

    public static function getModelLabel(): string
    {
        return _nav('model_page');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('model_pages');
    }

    public static function form(Form $form): Form
    {
        $parent = $form->getRecord()->parent;
        $parentField = [
            Hidden::make('parent_id')->default($parent ? $parent->id : null),
        ];
        $layoutField = [Select::make('layout_id')->relationship('layout', 'name')->nullable()];
        $customFields = [];

        if ($parent) {
            $section = MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->first();
            if ($section) {
                $layoutField = [];
                if ($parent->parent_id && $section->parent_id == $parent->parent_id) {
                    $parentField = [Select::make('parent_id')->options(Page::query()->where('parent_id', $parent->parent_id)->pluck('name', 'id'))->required()];
                }
            }
            if (MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->exists()) {
                $parent = MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->first();
                if ($parent->custom_fields && is_array($parent->custom_fields)) {
                    foreach ($parent->custom_fields as $field_) {
                        if (is_array($field_) && isset($field_['name']) && isset($field_['type'])) {
                            $array_to_field = ArrayToField::make($field_, 'custom.');
                            $customFields = array_merge($customFields, SchemaBuilder::make($array_to_field));
                        }
                    }
                }
            }
        }
        $isRequired = true;
        if (! Page::query()->where('slug', '')->exists() || $form->getRecord() && $form->getRecord()->slug === '') {
            $isRequired = false;
        }

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Schema::getReactiveName()->suffixActions([
                            ActionsAction::make(_fields('translates'))
                                ->hidden(function () {
                                    return ! is_multi_lang();
                                })
                                ->icon(function ($record) {
                                    if ($record->translatable()->count() > 0) {
                                        return 'heroicon-o-check-circle';
                                    }

                                    return 'heroicon-o-exclamation-circle';
                                })->form(function ($form) {
                                    $fields = [];
                                    $languages = get_active_languages();
                                    foreach ($languages as $language) {
                                        $fields[] = TextInput::make($language->slug . '.name')->label(_fields('name') . ' (' . $language->name . ')');
                                    }

                                    return $form->schema($fields);
                                })->fillForm(function ($record) {
                                    $translates = [];
                                    $languages = get_active_languages();
                                    foreach ($languages as $language) {
                                        $translates[$language->slug] = [
                                            'name' => $record->translatable()->where('language_id', $language->id)->first()->value ?? '',
                                        ];
                                    }

                                    return $translates;
                                })->action(function ($record, $data) {
                                    foreach (get_active_languages() as $lang) {
                                        $name = $data[$lang->slug]['name'] ?? '';
                                        if ($name == '') {
                                            Translate::query()->where([
                                                'language_id' => $lang->id,
                                                'entity_id' => $record->id,
                                                'entity_type' => config('shared.page_model', Page::class),
                                            ])->delete();

                                            continue;
                                        }
                                        Translate::query()->updateOrCreate([
                                            'language_id' => $lang->id,
                                            'entity_id' => $record->id,
                                            'entity_type' => config('shared.page_model', Page::class),
                                        ], ['value' => $name]);
                                    }
                                    Notification::make()->success()->title(_actions('saved'))->send();
                                }),
                        ]),
                        Schema::getSlug(Page::getDb(), $isRequired),
                        Schema::getStatus(),
                        Schema::getSorting(),
                        Schema::getImage(path: $form->getRecord() ? ('pages/' . $form->getRecord()->slug) : 'pages/temp'),
                        Schema::getImage(name: 'banner', path: $form->getRecord() ? ('pages/banners/' . $form->getRecord()->slug) : 'pages/banners/temp'),
                        ...$parentField,
                        ...$layoutField,
                        ...$customFields,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $parentCol = [];

        $activeTab = request('activeTab');
        if ($activeTab && strlen($activeTab) > 0 && ! str_contains($activeTab, 'Categories')) {
            $parentCol = [TextColumn::make('parent.name')->label(_fields('parent'))];
        }

        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $menuSections = MenuSection::query()->pluck('parent_id')->toArray();
                $query->withoutGlobalScopes()->whereNotIn('id', $menuSections);
            })
            ->columns([
                TableSchema::getName()->limit(50)->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();
                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }

                    return $state;
                }),
                ImageColumn::make('image')->label(_columns('image'))->defaultImageUrl(no_image())->square(),
                TableSchema::getStatus()->disabled(function ($record) {
                    return $record->slug == '';
                }),
                TableSchema::getSorting(),
                TableSchema::getViews(),
                ...$parentCol,
                TableSchema::getUpdatedAt(),
            ])
            ->filters([
                TableSchema::getFilterStatus(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
                Action::make('View')
                    ->label(_actions('view'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->iconButton()
                    ->url(function ($record) {
                        return $record->route();
                    })->openUrlInNewTab(),
            ])
            ->reorderable('sorting')
            ->headerActions([
                // Schema::helpAction('Static page help text')->hidden(function () {
                //     return (bool) request('parent');
                // }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Schema::getSeoAndTemplateRelationGroup(),
            ...config('shared.admin.page_relations', []),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return $record->slug && strlen($record->slug) > 0;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaticPages::route('/'),
            'edit' => Pages\EditStaticPage::route('/{record}/edit'),
            'seo' => Pages\EditSeo::route('/{record}/seo'),
            'template' => Pages\EditTemplate::route('/{record}/template'),
            'menu' => Pages\EditMenuSection::route('/{record}/menu'),
            'layout-settings' => Pages\EditLayoutSettings::route('/{record}/layout-settings'),
        ];
    }

    public static function getRecordSubNavigation($page): array
    {
        $section = MenuSection::query()->where('parent_id', $page->record->id)->first();
        $items = [
            Pages\EditStaticPage::class,
            Pages\EditSeo::class,
            Pages\EditTemplate::class,
            Pages\EditLayoutSettings::class,
        ];
        if ($section) {
            $items[] = Pages\EditMenuSection::class;
        }

        return $page->generateNavigationItems($items);
    }

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;
}
