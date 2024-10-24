<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages as Pages;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Helper;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class StaticPageResource extends Resource
{
    protected static ?string $model = Page::class;

    public static function getNavigationGroup(): ?string
    {
        return _nav('pages');
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery()->withoutGlobalScopes();
    //     $menuSections = MenuSection::query()->pluck('parent_id')->toArray();
    //     // $query->whereNotIn('id', $menuSections);

    //     return $query;
    // }

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
        $parent = $form->getRecord()->parent_id;
        $customFields = [];
        if (MenuSection::query()->where('parent_id', $parent)->exists()) {
            $parent = MenuSection::query()->where('parent_id', $parent)->first();
            $fields_id = (int) $parent->custom_fields ?? null;
            if ($fields_id !== null && $fields_id != '') {
                $fieldSchema = _config()->getCustomFields()[$fields_id];
                if (is_array($fieldSchema) && isset($fieldSchema['schema'])) {
                    $fields = $fieldSchema['schema'];
                    foreach ($fields as &$field) {
                        $newVar = Helper::getVariableSchema($field);
                        $customFields = array_merge($customFields, Helper::parseVariableByType($newVar, 'custom.'));
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
                        Schema::getReactiveName(),
                        Schema::getSlug(Page::getDb(), $isRequired),
                        Schema::getStatus(),
                        Schema::getSorting(),
                        Schema::getImage(),
                        Select::make('parent_id')
                            ->label(_fields('parent'))
                            ->relationship('parent', 'name')->nullable()->default(function () {
                                return request('parent') ?? null;
                            })->hidden((bool) request('parent'))->live(),
                        // Schema::getRepeater('nav_settings.fields')->schema([
                        //     TextInput::make('name')->label(_fields('name'))->required(),
                        //     TextInput::make('value')->label(_fields('value'))->required(),
                        // ])->default([]),
                        ...$customFields,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $menuSections = MenuSection::query()->pluck('parent_id')->toArray();
                $query->withoutGlobalScopes()->whereNotIn('id', $menuSections);
            })
            ->columns([
                TableSchema::getName(),
                TableSchema::getStatus(),
                TableSchema::getSorting(),
                TableSchema::getViews(),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([
                TableSchema::getFilterStatus(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('View')
                    ->label(_actions('view'))
                    ->icon('heroicon-o-eye')
                    ->url(function ($record) {
                        return '/'.$record->slug;
                    })->openUrlInNewTab(),
            ])
            ->reorderable('sorting')
            ->headerActions([
                Schema::helpAction('Static page help text')->hidden(function () {
                    return (bool) request('parent');
                }),
                Tables\Actions\Action::make('Template')
                    ->label(_actions('template'))
                    ->slideOver()
                    ->icon('heroicon-o-cog')
                    ->fillForm(function (): array {
                        return [
                            'template' => _settings('static_page_template', []),
                        ];
                    })
                    ->action(function (array $data): void {
                        setting([
                            sconfig('static_page_template') => $data['template'],
                        ]);
                    })
                    ->hidden(function () {
                        return (bool) request('parent');
                    })
                    ->form(function ($form) {
                        return $form
                            ->schema([
                                Section::make('')->schema([
                                    Schema::getTemplateBuilder()->label(_fields('template')),
                                ]),
                            ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [Schema::getSeoAndTemplateRelationGroup()];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaticPages::route('/'),
            // 'create' => Pages\CreateStaticPage::route('/create'),
            // 'create-page' => Pages\CreateStaticPage::route('/create'),
            'edit' => Pages\EditStaticPage::route('/{record}/edit'),
        ];
    }
}
