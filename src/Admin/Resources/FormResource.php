<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\FormResource\Pages;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form as ModelForm;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class FormResource extends BaseResource
{
    protected static ?string $model = ModelForm::class;

    protected static ?int $navigationSort = 3;

    public static string $resourceLabel = 'form';

    public static ?string $resourceGroup = 'system';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getFormSchema(Form $form): array
    {
        $buttons = [];
        $notification = [];
        $emailText = [];
        foreach (get_active_languages() as $lang) {
            $buttons[] = Hidden::make('data.'.$lang->slug.'.button');
            $notification[] = Hidden::make('data.'.$lang->slug.'.notification');
        }

        return [
            Section::make('')->schema([
                Schema::getName(true)->maxLength(255)->suffixActions([Schema::getTranslateAction()]),
                Repeater::make('fields')
                    ->schema([
                        Forms\Components\Select::make('field')
                            ->label(_fields('field_type'))
                            ->options(Field::query()->pluck('name', 'id')->toArray())->required()->native(false)->searchable(true)->live(debounce: 250),
                        Forms\Components\Toggle::make('is_required')
                            ->label(_fields('is_required'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
                Forms\Components\TextInput::make('data.button')
                    ->formatStateUsing(fn (?string $state): string => blank($state) ? 'Submit' : $state)
                    ->label(_fields('button'))
                    ->default('Submit')
                    ->maxLength(255)
                    ->suffixAction(Action::make('translate')->icon('heroicon-m-language')->action(function ($data, $set) {
                        foreach ($data as $key => $value) {
                            $set('data.'.$key.'.button', $value);
                        }
                    })->fillForm(function ($record) {
                        $data = [];
                        foreach (get_active_languages() as $lang) {
                            $data[$lang->slug] = $record->data[$lang->slug]['button'] ?? '';
                        }

                        return $data;
                    })->form(function ($form) {
                        $schema = [];
                        foreach (get_active_languages() as $lang) {
                            $schema[] = TextInput::make($lang->slug)->label(_fields('button').' ('.$lang->name.')')->default('')->maxLength(255);
                        }

                        return $form->schema($schema);
                    })),
                Forms\Components\TextInput::make('data.notification')
                    ->formatStateUsing(fn (?string $state): string => blank($state) ? 'Form submitted successfully' : $state)
                    ->label(_fields('notification'))
                    ->default('Form submitted successfully')
                    ->maxLength(255)
                    ->suffixAction(Action::make('translate')->icon('heroicon-m-language')->action(function ($data, $set) {
                        foreach ($data as $key => $value) {
                            $set('data.'.$key.'.notification', $value);
                        }
                    })->fillForm(function ($record) {
                        $data = [];
                        foreach (get_active_languages() as $lang) {
                            $data[$lang->slug] = $record->data[$lang->slug]['notification'] ?? '';
                        }

                        return $data;
                    })->form(function ($form) {
                        $schema = [];
                        foreach (get_active_languages() as $lang) {
                            $schema[] = TextInput::make($lang->slug)->label(_fields('notification').' ('.$lang->name.')')->default('')->maxLength(255);
                        }

                        return $form->schema($schema);
                    })),
                Section::make(_fields('additional'))->schema([
                    Forms\Components\TextInput::make('html_id')
                        ->label(_fields('html_id'))
                        ->default('')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('class')
                        ->label(_fields('html_class'))
                        ->default('')
                        ->maxLength(255),
                ])->collapsible()->collapsed(),
                ...$buttons,
                ...$notification,
            ]),
        ];
    }

    public static function getTableColumns(Table $table): array
    {
        return [
            TableSchema::getName(),
            Tables\Columns\TextColumn::make('code')
                ->copyable()
                ->label(_columns('code'))
                ->searchable(),
            TableSchema::getUpdatedAt(),
        ];
    }

    public static function getResourcePages(): array
    {
        return [
            'index' => Pages\ListForms::route('/'),
            'edit' => Pages\EditForm::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
