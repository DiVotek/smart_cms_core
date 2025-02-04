<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SmartCms\Core\Admin\Resources\FieldResource\Pages;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('communication');
    }

    public static function getModelLabel(): string
    {
        return _nav('field');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('fields');
    }

    public static function form(Form $form): Form
    {

        $label = [];
        $placeholder = [];
        $description = [];
        $options = [];
        $mask = [];
        foreach (get_active_languages() as $lang) {
            $label[] = Forms\Components\TextInput::make('label.'.$lang->slug)
                ->label(_fields('label').' ('.$lang->name.')')
                ->required();
            $placeholder[] = Forms\Components\TextInput::make('placeholder.'.$lang->slug)
                ->label(_fields('placeholder').' ('.$lang->name.')');
            $description[] = Forms\Components\Textarea::make('description.'.$lang->slug)
                ->label(_fields('description').' ('.$lang->name.')');
            $options[] = Forms\Components\TextInput::make('mask.'.$lang->slug)
                ->label(_fields('options').' ('.$lang->name.')');
            $mask[] = Forms\Components\TextInput::make('mask.'.$lang->slug)->label(_fields('mask').' ('.$lang->name.')');
        }

        return $form
            ->schema([
                Section::make('')->schema([
                    Schema::getName()->live()->afterStateUpdated(function ($get, $set) {
                        if (! $get('html_id')) {
                            $set('html_id', \Illuminate\Support\Str::slug($get('name')).'-'.\Illuminate\Support\Str::random(5));
                        }
                    }),
                    Forms\Components\Select::make('type')
                        ->label(_fields('field_type'))
                        ->options([
                            'text' => 'Text',
                            'textarea' => 'Textarea',
                            'select' => 'Select',
                            'radio' => 'Radio',
                            'checkbox' => 'Checkbox',
                            'file' => 'File',
                            'date' => 'Date',
                            'email' => 'Email',
                            'number' => 'Number',
                            'tel' => 'Tel',
                            'url' => 'Url',
                        ])->required()->native(false)->searchable(true)->live(debounce: 250),
                    Repeater::make('options')->schema($options)
                        ->nullable()->hidden(fn ($get) => ! in_array($get('type'), ['select', 'radio', 'checkbox'])),
                    Forms\Components\Toggle::make('required')
                        ->label(_fields('required'))
                        ->default(false),
                    ...$label,
                    ...$placeholder,
                    ...$description,
                    ...$mask,
                    Forms\Components\TextInput::make('validation')
                        ->label(_fields('validation')),
                    Forms\Components\TextInput::make('html_id')
                        ->label(_fields('html_id'))->hidden(),
                ]),
                // Section::make('additional')->schema([

                //     Forms\Components\TextInput::make('class')
                //         ->label(_fields('class'))->default(''),
                //     Forms\Components\TextInput::make('style')
                //         ->label(_fields('style')),
                // ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(_columns('username'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('required')
                    ->label(_columns('required')),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFields::route('/'),
            'create' => Pages\CreateField::route('/create'),
            'edit' => Pages\EditFields::route('/{record}/edit'),
        ];
    }
}
