<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SmartCms\Core\Admin\Resources\FormResource\Pages;
use SmartCms\Core\Models\Field;
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
        return _nav('form');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('forms');
    }

    public static function form(Form $form): Form
    {
        $button = [];
        $notification = [];
        $emailText = [];
        foreach (get_active_languages() as $lang) {
            $button[] = TextInput::make('button.'.$lang->slug)->label(_fields('button').' ('.$lang->name.')')->default('Submit')->maxLength(255);
            $notification[] = TextInput::make('notification.'.$lang->slug)->label(_fields('notification').' ('.$lang->name.')')->default('Form submitted successfully')->maxLength(255);
            $emailText[] = Textarea::make('email_text.'.$lang->slug)->label(_fields('email_text').' ('.$lang->name.')')->default('Form submitted successfully')->maxLength(255);
        }

        return $form
            ->schema([
                Section::make('')->schema([
                    Schema::getName(true)->maxLength(255),
                    Repeater::make('fields')
                        ->label(_fields('group_fields'))
                        ->schema([
                            Repeater::make('fields')
                                ->schema([
                                    Forms\Components\Select::make('field')
                                        ->label(_fields('field_type'))
                                        ->options(Field::query()->pluck('name', 'id')->toArray())->required()->native(false)->searchable(true)->live(debounce: 250),
                                ]),
                            TextInput::make('class')
                                ->label(_fields('html_class'))
                                ->default('')
                                ->maxLength(255),
                        ]),
                    ...$button,
                    ...$notification,
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
                    Fieldset::make(_fields('email'))->schema([
                        Forms\Components\Toggle::make('is_send_email')
                            ->label(_fields('send_email'))
                            ->default(false),
                        ...$emailText,
                        Forms\Components\Select::make('email_template')
                            ->label(_fields('email_template'))
                            // ->options(Helper::getEmailTemplates())
                            ->default(1)->hidden(),
                    ])->columns(1),
                ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableSchema::getName(),
                Tables\Columns\TextColumn::make('code')
                    ->label(_columns('code'))
                    ->searchable(),
                // Tables\Columns\TextColumn::make('style')
                //     ->label(_columns('style'))
                //     ->searchable(),
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
