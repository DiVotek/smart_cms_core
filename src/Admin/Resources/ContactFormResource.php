<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Schmeits\FilamentCharacterCounter\Forms\Components\Textarea;
use SmartCms\Core\Admin\Resources\ContactFormResource\Pages;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form as ModelsForm;
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
                    KeyValue::make('data')
                        ->label(_fields('user_data'))
                        ->addable(false)->deletable(false)->label(null)->editableKeys(false),
                    Select::make('status')
                        ->label(_fields('status'))
                        ->options(fn() => ContactForm::getStatuses()),
                    Textarea::make('comment')->label(_fields('comment'))->maxLength(255),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $fields = Field::query()->pluck('name', 'id')->toArray();
        $columns = [];
        foreach ($fields as $field) {
            $columns[] = Tables\Columns\TextColumn::make('data.' . $field)->label($field)->toggleable()->toggledHiddenByDefault()->sortable()->copyable();
        }
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('form.name')->label(_columns('form')),
                SelectColumn::make('status')
                    ->label(_columns('status'))
                    ->options(fn() => ContactForm::getStatuses()),
                ...$columns,
                TableSchema::getUpdatedAt(),
                TableSchema::getCreatedAt(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(_columns('status'))
                    ->options(fn() => ContactForm::getStatuses()),
                SelectFilter::make('form_id')
                    ->label(_columns('form'))
                    ->options(fn() => ModelsForm::query()->pluck('name', 'id')->toArray()),
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
