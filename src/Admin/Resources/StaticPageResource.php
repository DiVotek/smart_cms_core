<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder as SchemaBuilder;
use SmartCms\Core\Services\TableSchema;
use SmartCms\Core\Traits\HasHooks;

class StaticPageResource extends BaseResource
{
    use HasHooks;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $model = Page::class;

    public static ?string $resourceGroup = null;

    public static string $resourceLabel = 'model_page';

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-m-book-open';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getFormSchema(Form $form): array
    {
        $parent = $form->getRecord()->parent;
        $parentField = [];
        $layoutField = [
            Select::make('layout_id')
                ->suffixActions([
                    Actions\Action::make('reset_template')->icon('heroicon-o-arrow-path')
                        ->action(function () use ($form) {
                            $form->getRecord()->update([
                                'layout_settings' => null,
                            ]);
                        })
                        ->requiresConfirmation()
                        ->hidden(function () use ($form) {
                            return $form->getRecord()->layout_id === null || $form->getRecord()->layout_settings == null;
                        }),
                    Actions\Action::make('template')->icon('heroicon-o-cog')
                        ->fillForm(function () use ($form) {
                            $record = $form->getRecord();
                            if ($record->layout_settings) {
                                return ['layout_settings' => $record->layout_settings];
                            }

                            return ['layout_settings' => $record->layout?->value ?? []];
                        })
                        ->form(function () use ($form) {
                            $schema = $form->getRecord()->getLayoutSettingsForm();
                            $schema[] = Placeholder::make('layout_settings_placeholder')
                                ->hiddenLabel()
                                ->content(_actions('empty_layout_settings'))
                                ->visible(count($schema) === 0);

                            return $schema;
                        })
                        ->action(function ($data) use ($form) {
                            $record = $form->getRecord();
                            if ($record->layout && $record->layout->value == $data['layout_settings']) {
                                return $record;
                            }

                            $record->update([
                                'layout_settings' => $data['layout_settings'] ?? [],
                            ]);
                        }),
                ])
                ->relationship('layout', 'name', function ($query) use ($form) {
                    $query = $query->withoutGlobalScopes();
                    $cloned = $query->clone();
                    self::applyHook('page.layout', $cloned, $form->getRecord());
                    if ($cloned != $query) {
                        return $cloned;
                    } else {
                        if (MenuSection::query()->where('parent_id', $form->getRecord()->id)->exists()) {
                            return $query->where('path', 'like', '%groups.%')
                                ->whereRaw("path NOT REGEXP '\\\..*\\\.'");
                        }
                        if (! $form->getRecord()->parent_id) {
                            return $query->where('path', 'like', '%pages.%');
                        }
                    }

                    return $query;
                })->nullable()->disabled(function ($record) {
                    return $record->parent_id;
                }),
        ];
        $customFields = [];

        if ($parent) {
            $section = MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->first();
            if ($section) {
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
        $imagePath = '';
        if ($form->getRecord()->slug) {
            $imagePath = 'pages/' . $form->getRecord()->slug;
        }

        return [
            Forms\Components\Group::make([
                Forms\Components\Group::make([
                    Forms\Components\Section::make()
                        ->schema([
                            Schema::getReactiveName()->suffixActions([
                                Schema::getTranslateAction(),
                            ]),
                            Schema::getSlug(Page::getDb(), $isRequired)->helperText(''),
                        ])->columns(2),
                    Forms\Components\Section::make(_fields('images'))->schema([
                        Schema::getImage(path: $imagePath),
                        Schema::getImage(name: 'banner', path: $imagePath),
                    ])->collapsible(),
                    Forms\Components\Section::make()->schema([...$layoutField]),
                ])->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label(_fields('created_at'))
                                    ->inlineLabel()
                                    ->content(fn($record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label(_fields('updated_at'))
                                    ->translateLabel()
                                    ->inlineLabel()
                                    ->content(fn($record): ?string => $record->updated_at?->diffForHumans()),
                                Schema::getStatus()->hidden(function ($record) {
                                    return $record->slug == '' || MenuSection::query()->where('parent_id', $record->id)->exists();
                                }),
                            ])->columns(1),
                        Forms\Components\Section::make()->schema([
                            Schema::getSorting()->hidden(),
                            ...$parentField,
                        ])->hidden(function () use ($parentField) {
                            return count($parentField) == 0;
                        }),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3),
        ];
    }

    protected static function getTableFilters(): array
    {
        return [
            TableSchema::getFilterStatus(),
        ];
    }

    protected static function getTableActions(Table $table): array
    {
        return [
            Action::make('View')
                ->label(_actions('view'))
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->iconButton()
                ->url(function ($record) {
                    return $record->route();
                })->openUrlInNewTab(),
        ];
    }

    protected static function getTableColumns(Table $table): array
    {
        $parentCol = [];

        $activeTab = request('activeTab');
        if ($activeTab && strlen($activeTab) > 0 && ! str_contains($activeTab, 'Categories')) {
            $parentCol = [TextColumn::make('parent.name')->label(_fields('parent'))];
        }

        return [
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
        ];
    }

    protected static function getResourcePages(): array
    {
        return [
            'index' => Pages\ListStaticPages::route('/'),
            'edit' => Pages\EditStaticPage::route('/{record}/edit'),
            'seo' => Pages\EditSeo::route('/{record}/seo'),
            'template' => Pages\EditTemplate::route('/{record}/template'),
            'menu' => Pages\EditMenuSection::route('/{record}/menu'),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return $record->slug && strlen($record->slug) > 0 && ! MenuSection::query()->where('parent_id', $record->id)->exists();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getResourceSubNavigation($page): array
    {
        $items = [
            Pages\EditStaticPage::class,
            Pages\EditSeo::class,
            Pages\EditTemplate::class,
        ];
        $section = MenuSection::query()->where('parent_id', $page->record->id)->first();
        if ($section) {
            $items[] = Pages\EditMenuSection::class;
        }

        return $items;
    }
}
