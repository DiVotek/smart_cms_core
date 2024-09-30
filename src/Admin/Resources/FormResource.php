<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SmartCms\Core\Admin\Resources\FormResource\Pages;
use SmartCms\Core\Models\Form as ModelForm;
use SmartCms\Core\Services\Helper;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class FormResource extends Resource
{
    protected static ?string $model = ModelForm::class;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('communication');
    }
    public static function getModelLabel(): string
    {
        return _nav('form');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('forms');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Schema::getName(true)->maxLength(255),
                    Repeater::make('fields')
                        ->schema([
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
                            Textarea::make('options')
                                ->nullable()
                                ->rows(3)->hidden(fn($get) => ! in_array($get('type'), ['select', 'radio', 'checkbox'])),
                            Schema::getName(true)->maxLength(255),
                            // Forms\Components\TextInput::make('label')
                            //     ->required()
                            //     ->maxLength(255),
                            Forms\Components\Toggle::make('required')
                                ->label(_fields('required'))
                                ->default(false),
                            // Forms\Components\TextInput::make('placeholder')
                            //     ->required()
                            //     ->maxLength(255),

                        ]),
                ]),
                Section::make(_fields('additional'))->schema([
                    Forms\Components\TextInput::make('html_id')
                        ->label(_fields('html_id'))
                        ->default('')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('class')
                        ->label(_fields('html_class'))
                        ->default('')
                        ->maxLength(255),
                    Forms\Components\Select::make('style')
                        ->label(_fields('style'))
                        ->options(Helper::getFormTemplates())
                        ->default(1)
                        ->required(),
                ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(_columns('code'))
                    ->searchable(),
                TableSchema::getName(),
                Tables\Columns\TextColumn::make('html_id')
                    ->label(_columns('html_id'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('class')
                    ->label(_columns('class'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('style')
                    ->label(_columns('style'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(_columns('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForms::route('/'),
            'create' => Pages\CreateForm::route('/create'),
            'edit' => Pages\EditForm::route('/{record}/edit'),
        ];
    }
}
