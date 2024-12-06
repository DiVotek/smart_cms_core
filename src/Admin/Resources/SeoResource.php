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
        $keywordPhrase = '';
        $fieldResults = [
            'title' => false,
            'heading' => false,
            'description' => false,
            'summary' => false,
            'content' => false,
        ];
        $checkKeywordPhrase = function ($fieldValue, $keywordPhrase) {
            return strpos($fieldValue, $keywordPhrase) !== false;
        };

        $language = Hidden::make('language_id');
        if (is_multi_lang()) {
            $language = Schema::getSelect('language_id')->relationship('language', 'name');
        }
        $language = $language->default(main_lang_id());

        return $form
            ->schema([
                Section::make('')->schema([
                    $language,
                    TextInput::make('keyword_phrase')
                        ->label(_fields('seo_keyword_phrase'))
                        ->translatable()
                        ->rules('string', 'max:255')
                        ->maxLength(255)
                        ->afterStateUpdated(function ($state) use ($checkKeywordPhrase, &$fieldResults, $keywordPhrase) {
                            $fieldResults['title'] = $checkKeywordPhrase($state, $keywordPhrase);
                            $fieldResults['heading'] = $checkKeywordPhrase($state, $keywordPhrase);
                            $fieldResults['description'] = $checkKeywordPhrase($state, $keywordPhrase);
                            $fieldResults['summary'] = $checkKeywordPhrase($state, $keywordPhrase);
                            $fieldResults['content'] = $checkKeywordPhrase($state, $keywordPhrase);
                        }),
                    TextInput::make('title')
                        ->label(_fields('seo_title'))
                        ->required()
                        ->translatable()
                        ->rules('string', 'max:255')
                        ->characterLimit(255)
                        ->maxLength(255)
                        ->afterStateUpdated(function ($state) use ($checkKeywordPhrase, &$fieldResults, $keywordPhrase) {
                            $fieldResults['title'] = $checkKeywordPhrase($state, $keywordPhrase);
                        }),
                    TextInput::make('heading')
                        ->label(_fields('seo_heading'))
                        ->translatable()
                        ->rules('string', 'max:255')
                        ->characterLimit(255)
                        ->maxLength(255)
                        ->afterStateUpdated(function ($state) use ($checkKeywordPhrase, &$fieldResults, $keywordPhrase) {
                            $fieldResults['heading'] = $checkKeywordPhrase($state, $keywordPhrase);
                        }),
                    Textarea::make('description')
                        ->label(_fields('seo_description'))
                        ->required()
                        ->rules('string', 'max:255')
                        ->translatable()
                        ->characterLimit(255)
                        ->maxLength(255)
                        ->afterStateUpdated(function ($state) use ($checkKeywordPhrase, &$fieldResults, $keywordPhrase) {
                            $fieldResults['description'] = $checkKeywordPhrase($state, $keywordPhrase);
                        }),
                    RichEditor::make('summary')
                        ->label(_fields('seo_summary'))
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
                        ])
                        ->afterStateUpdated(function ($state) use ($checkKeywordPhrase, &$fieldResults, $keywordPhrase) {
                            $fieldResults['summary'] = $checkKeywordPhrase($state, $keywordPhrase);
                        }),
                    RichEditor::make('content')
                        ->label(_fields('seo_content'))
                        ->translatable()
                        ->rules('string')
                        ->afterStateUpdated(function ($state) use ($checkKeywordPhrase, &$fieldResults, $keywordPhrase) {
                            $fieldResults['content'] = $checkKeywordPhrase($state, $keywordPhrase);
                        }),
                ]),
                Section::make('Keyword Phrase Check Results')->schema([
                    TextInput::make('title_check_result')
                        ->label('Title Check Result')
                        ->getStateUsing(function () use ($fieldResults) {
                            return $fieldResults['title'] ? 'Keyword phrase found.' : 'Keyword phrase not found.';
                        })
                        ->disabled(),

                    TextInput::make('heading_check_result')
                        ->label('Heading Check Result')
                        ->getStateUsing(function () use ($fieldResults) {
                            return $fieldResults['heading'] ? 'Keyword phrase found.' : 'Keyword phrase not found.';
                        })
                        ->disabled(),

                    TextInput::make('description_check_result')
                        ->label('Description Check Result')
                        ->getStateUsing(function () use ($fieldResults) {
                            return $fieldResults['description'] ? 'Keyword phrase found.' : 'Keyword phrase not found.';
                        })
                        ->disabled(),

                    TextInput::make('summary_check_result')
                        ->label('Summary Check Result')
                        ->getStateUsing(function () use ($fieldResults) {
                            return $fieldResults['summary'] ? 'Keyword phrase found.' : 'Keyword phrase not found.';
                        })
                        ->disabled(),

                    TextInput::make('content_check_result')
                        ->label('Content Check Result')
                        ->getStateUsing(function () use ($fieldResults) {
                            return $fieldResults['content'] ? 'Keyword phrase found.' : 'Keyword phrase not found.';
                        })
                        ->disabled(),
                ])->collapsible(),
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
                            'indexation' => _settings('indexation'),
                            'gtm' => _settings('gtm'),
                            'title' => _settings('title_mod'),
                            'description' => _settings('description_mod'),
                            'meta' => _settings('custom_meta'),
                            'scripts' => _settings('custom_scripts'),
                        ];
                    })
                    ->action(function (array $data): void {
                        setting([
                            sconfig('indexation') => $data['indexation'] ?? false,
                            sconfig('gtm') => $data['gtm'] ?? '',
                            sconfig('title_mod') => $data['title'] ?? [],
                            sconfig('description_mod') => $data['description'] ?? [],
                            sconfig('custom_meta') => $data['meta'] ?? [],
                            sconfig('custom_scripts') => $data['scripts'] ?? [],
                        ]);
                    })
                    ->form(function ($form) {
                        return $form
                            ->schema([
                                Section::make('')->schema([
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

    protected static function checkKeywordPhrase($text, $keywordPhrase)
    {
        if (stripos($text, $keywordPhrase) !== false) {
            return true;
        }

        return false;
    }
}
