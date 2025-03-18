<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Schmeits\FilamentCharacterCounter\Forms\Components\Textarea;
use Schmeits\FilamentCharacterCounter\Forms\Components\TextInput;
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
                $language,
                TextInput::make('title')
                    ->label(_fields('seo_title'))
                    ->required()
                    ->translatable()
                    ->rules('string', 'max:255')
                    ->characterLimit(255)
                    ->maxLength(255),
                TextInput::make('heading')
                    ->label(_fields('seo_heading'))
                    ->translatable()
                    ->rules('string', 'max:255')
                    ->characterLimit(255)
                    ->maxLength(255),
                Textarea::make('description')
                    ->label(_fields('seo_description'))
                    ->required()
                    ->rules('string', 'max:255')
                    ->translatable()
                    ->characterLimit(255)
                    ->maxLength(255),
                Textarea::make('summary')
                    ->label(_fields('seo_summary'))
                    ->translatable()
                    ->rules('string', 'max:500')
                    ->maxLength(500),
                RichEditor::make('content')
                    ->label(_fields('seo_content'))
                    ->translatable()
                    ->rules('string'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                if (! is_multi_lang()) {
                    $query->where('language_id', main_lang_id());
                }
            })
            ->columns([
                TextColumn::make('title')->label(_columns('title'))->limit(50),
                TextColumn::make('description')->label(_columns('description'))->limit(50),
                TextColumn::make('language.name')->label(_columns('language')),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
