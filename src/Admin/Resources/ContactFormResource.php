<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Schmeits\FilamentCharacterCounter\Forms\Components\Textarea;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\ContactFormResource\Pages;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form as ModelsForm;
use SmartCms\Core\Services\TableSchema;

class ContactFormResource extends BaseResource
{
    protected static ?string $model = ContactForm::class;

    protected static ?int $navigationSort = 1;

    public static string $resourceLabel = 'contact_form';

    public static ?string $resourceGroup = null;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getFormSchema(Form $form): array
    {
        return [
            Select::make('status')
                ->label(_fields('status'))
                ->options(fn () => ContactForm::getStatuses()),
            KeyValue::make('data')
                ->label(_fields('user_data'))
                ->addable(false)->deletable(false)->label(null)->editableKeys(false),
            Textarea::make('comment')->label(_fields('comment'))->maxLength(255),
        ];
    }

    public static function getTableColumns(Table $table): array
    {
        $fields = Field::query()->pluck('name', 'id')->toArray();
        $columns = [];
        foreach ($fields as $field) {
            $columns[] = Tables\Columns\TextColumn::make('data.'.$field)->label($field)->toggleable()->toggledHiddenByDefault()->sortable()->copyable();
        }

        return [
            TextColumn::make('id')->label(_columns('number'))->sortable(),
            TextColumn::make('form.name')->label(_columns('form')),
            SelectColumn::make('status')
                ->label(_columns('status'))
                ->options(fn () => ContactForm::getStatuses()),
            ...$columns,
            TableSchema::getUpdatedAt(),
            TableSchema::getCreatedAt(),
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->label(_columns('status'))
                ->options(fn () => ContactForm::getStatuses()),
            SelectFilter::make('form_id')
                ->label(_columns('form'))
                ->options(fn () => ModelsForm::query()->pluck('name', 'id')->toArray()),
        ];
    }

    public static function getResourcePages(): array
    {
        return [
            'index' => Pages\ManageContactForms::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
