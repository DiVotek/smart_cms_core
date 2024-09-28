<?php

namespace SmartCms\Core\Admin\Resources;

use SmartCms\Core\Admin\Resources\FormResource\Pages;
use SmartCms\Core\Admin\Resources\FormResource\RelationManagers;
use SmartCms\Core\Models\Form as ModelForm;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\File;
use SmartCms\Core\Services\Helper;

class FormResource extends Resource
{
    protected static ?string $model = ModelForm::class;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Communication');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Repeater::make('fields')
                        ->schema([
                            Forms\Components\Select::make('type')->options([
                                'text' => 'Text',
                                'textarea' => 'Textarea',
                                'select' => 'Select',
                                'radio' => 'Radio',
                                'checkbox' => 'Checkbox',
                                'file' => 'File',
                                'date' => 'Date',
                                'email' => 'Email',
                                'number' => 'Number',
                                // 'password' => 'Password',
                                'tel' => 'Tel',
                                'url' => 'Url',
                            ])->required()->native(false)->searchable(true)->live(debounce: 250),
                            Textarea::make('options')
                                ->nullable()
                                ->rows(3)->hidden(fn ($get) => !in_array($get('type'), ['select', 'radio', 'checkbox'])),
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            // Forms\Components\TextInput::make('label')
                            //     ->required()
                            //     ->maxLength(255),
                            Forms\Components\Toggle::make('required')
                                ->default(false),
                            // Forms\Components\TextInput::make('placeholder')
                            //     ->required()
                            //     ->maxLength(255),

                        ]),
                ]),
                Section::make(__('Additional'))->schema([
                    Forms\Components\TextInput::make('html_id')
                        ->required()
                        ->default('')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('class')
                    ->default('')
                        ->maxLength(255),
                    Forms\Components\Select::make('style')
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('html_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('class')
                    ->searchable(),
                Tables\Columns\TextColumn::make('style')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
