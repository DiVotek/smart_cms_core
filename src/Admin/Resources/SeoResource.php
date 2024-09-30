<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Schmeits\FilamentCharacterCounter\Forms\Components\Textarea;
use Schmeits\FilamentCharacterCounter\Forms\Components\TextInput;
use SmartCms\Core\Admin\Resources\SeoResource\Pages;
use SmartCms\Core\Models\Seo;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class SeoResource extends Resource
{
    protected static ?string $model = Seo::class;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('seo');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return _nav('seo_model');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('seo_models');
    }

    public static function form(Form $form): Form
    {
        $language = Hidden::make('language_id');
        if (is_multi_lang()) {
            $language = Schema::getSelect('language_id')->relationship('language', 'name');
        }
        $language = $language->default(main_lang_id());

        return $form
            ->schema([
                Section::make('')->schema([
                    $language,
                    TextInput::make('title')
                        ->label(_fields('title'))
                        ->required()
                        ->translatable()
                        ->rules('string', 'max:255')
                        ->characterLimit(255)
                        ->maxLength(255),
                    TextInput::make('heading')
                        ->label(_fields('heading'))
                        ->translatable()
                        ->rules('string', 'max:255')
                        ->characterLimit(255)
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label(_fields('description'))
                        ->required()
                        ->rules('string', 'max:255')
                        ->translatable()
                        ->characterLimit(255)
                        ->maxLength(255),
                    RichEditor::make('summary')
                        ->label(_fields('summary'))
                        ->translatable()
                        ->rules('string', 'max:500')
                        ->maxLength(500)->toolbarButtons([
                            'blockquote',
                            'bold',
                            'italic',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                            'codeBlock',
                        ]),
                    RichEditor::make('content')
                        ->label(_fields('content'))
                        ->translatable()
                        ->rules('string'),
                ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        $columns = [
            TextColumn::make('title')->label(_columns('title'))->limit(50),
            TextColumn::make('description')->label(_columns('description'))->limit(50),
        ];
        if (is_multi_lang()) {
            $columns[] = TextColumn::make('language.name')->label(_columns('language'));
        }
        $columns[] = TableSchema::getUpdatedAt();

        return $table
            ->modifyQueryUsing(function ($query) {
                if (! is_multi_lang()) {
                    $query->where('language_id', main_lang_id());
                }
            })
            ->columns($columns)
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('Settings')
                    ->slideOver()
                    ->icon('heroicon-o-cog')
                    ->modal()
                    ->fillForm(function (): array {
                        return [
                            'indexation' => setting(config('settings.indexation')),
                            'gtm' => setting(config('settings.gtm')),
                            'title' => setting(config('settings.title_mod')),
                            'description' => setting(config('settings.description_mod')),
                            'meta' => setting(config('settings.custom_meta')),
                            'scripts' => setting(config('settings.custom_scripts')),
                        ];
                    })
                    ->action(function (array $data): void {
                        setting([
                            config('settings.indexation') => $data['indexation'] ?? false,
                            config('settings.gtm') => $data['gtm'] ?? '',
                            config('settings.title_mod') => $data['title'] ?? [],
                            config('settings.description_mod') => $data['description'] ?? [],
                            config('settings.custom_meta') => $data['meta'] ?? [],
                            config('settings.custom_scripts') => $data['scripts'] ?? [],
                        ]);
                    })
                    ->form(function ($form) {
                        return $form
                            ->schema([
                                Section::make('')->schema([
                                    Toggle::make('indexation')
                                        ->label(_actions('indexation'))
                                        ->helperText(_hints('Enable or disable indexation for search engines'))
                                        ->required(),
                                    TextInput::make('gtm')
                                        ->label(_actions('google_tag'))
                                        ->helperText(_hints('Enter your Google Tag Manager Id'))
                                        ->string(),
                                    Fieldset::make(_actions('title'))
                                        ->schema([
                                            TextInput::make('title.prefix')
                                                ->label(_actions('prefix'))
                                                ->string()
                                                ->helperText(_hints('Enter your title prefix')),
                                            TextInput::make('title.suffix')
                                                ->label(_actions('suffix'))
                                                ->helperText(_hints('Enter your title suffix'))
                                                ->string(),
                                        ]),
                                    Fieldset::make(_actions('description'))
                                        ->schema([
                                            TextInput::make('description.prefix')
                                                ->label(_actions('prefix'))
                                                ->string()
                                                ->helperText(_hints('Enter your description prefix')),
                                            TextInput::make('description.suffix')
                                                ->label(_actions('suffix'))
                                                ->helperText(_hints('Enter your description suffix'))
                                                ->string(),
                                        ]),
                                    Schema::getRepeater('meta')
                                        ->label(_actions('custom_meta'))
                                        ->schema([
                                            TextInput::make('name')
                                                ->label(_actions('name'))
                                                ->string(),
                                            TextInput::make('description')
                                                ->label(_actions('description'))
                                                ->string(),
                                            Textarea::make('meta_tags')
                                                ->label(_actions('meta_tags')),
                                        ])
                                        ->default([]),
                                    Schema::getRepeater('scripts')
                                        ->label(_actions('custom_scripts'))
                                        ->schema([
                                            TextInput::make('name')
                                                ->label(_actions('name'))
                                                ->string(),
                                            TextInput::make('description')
                                                ->label(_actions('description'))
                                                ->string(),
                                            Textarea::make('scripts')
                                                ->label(_actions('scripts')),
                                        ])
                                        ->default([]),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeos::route('/'),
            'create' => Pages\CreateSeo::route('/create'),
            'edit' => Pages\EditSeo::route('/{record}/edit'),
        ];
    }
}
