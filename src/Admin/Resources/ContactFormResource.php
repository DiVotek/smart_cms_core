<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use SmartCms\Core\Admin\Resources\ContactFormResource\Pages;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Services\TableSchema;

class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;

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
        return _nav('contact_form');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('contact_forms');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Select::make('status')
                        ->label(_fields('status'))
                        ->options(fn () => ContactForm::getStatuses()),
                    KeyValue::make('data')->addable(false)->deletable(false)->label(null)->editableKeys(false),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('form.name')->label(_columns('form')),
                IconColumn::make('status')
                    ->label(_columns('status'))
                    ->icon(fn (int $state): string => match ($state) {
                        ContactForm::STATUS_NEW => 'heroicon-o-bell-alert',
                        ContactForm::STATUS_VIEWED => 'heroicon-o-eye',
                        ContactForm::STATUS_CLOSED => 'heroicon-o-x',
                        default => 'heroicon-o-check-circle',
                    }),
                TableSchema::getUpdatedAt(),
                TableSchema::getCreatedAt(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make()->beforeFormFilled(function ($record) {
                    $record->data = array_filter($record->data, function ($key) {
                        return ! in_array($key, ['form_attributes', '_token', 'form']);
                    }, ARRAY_FILTER_USE_KEY);

                    return $record;
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContactForms::route('/'),
        ];
    }
}
