<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
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

    protected static ?int $navigationSort = 2;

    public static string $resourceLabel = 'form';

    public static ?string $resourceGroup = 'modules';

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
            Forms\Components\Group::make([
                Forms\Components\Group::make([
                    Forms\Components\Section::make()
                        ->schema([
                            Schema::getReactiveName()->suffixActions([
                                Schema::getTranslateAction(),
                            ]),
                        ])->columns(1),
                    Forms\Components\Section::make(_fields('fields'))
                        ->schema([
                            Repeater::make('fields')
                                ->hiddenLabel()
                                ->schema([
                                    Forms\Components\Select::make('field')
                                        ->label(_fields('field_type'))
                                        ->options(Field::query()->pluck('name', 'id')->toArray())->required()->native(false)->searchable(true)->live(debounce: 250),
                                    Forms\Components\Toggle::make('is_required')
                                        ->label(_fields('is_required'))
                                        ->default(true)
                                        ->inline(false),
                                ])->columns(2),
                        ]),
                    Forms\Components\Section::make()->schema([
                        ...$buttons,
                        ...$notification,
                        Forms\Components\TextInput::make('data.button')
                            ->formatStateUsing(fn (?string $state): string => blank($state) ? 'Submit' : $state)
                            ->label(_fields('button'))
                            ->default('Submit')
                            ->maxLength(255)
                            ->suffixAction(Action::make('translate')
                                ->badge(function ($get) {
                                    $counter = 0;
                                    foreach (get_active_languages() as $lang) {
                                        $button = $get('data.'.$lang->slug.'.button') ?? null;
                                        if ($button && strlen($button) > 0) {
                                            $counter++;
                                        }
                                    }

                                    return $counter;
                                })
                                ->icon('heroicon-m-language')->action(function ($data, $set) {
                                    foreach ($data as $key => $value) {
                                        $set('data.'.$key.'.button', $value);
                                    }
                                })
                                ->modalWidth(MaxWidth::TwoExtraLarge)
                                ->fillForm(function ($record) {
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
                            ->suffixAction(
                                Action::make('translate')->icon('heroicon-m-language')
                                    ->badge(function ($get) {
                                        $counter = 0;
                                        foreach (get_active_languages() as $lang) {
                                            $notification = $get('data.'.$lang->slug.'.notification') ?? null;
                                            if ($notification && strlen($notification) > 0) {
                                                $counter++;
                                            }
                                        }

                                        return $counter;
                                    })
                                    ->action(function ($data, $set) {
                                        foreach ($data as $key => $value) {
                                            $set('data.'.$key.'.notification', $value);
                                        }
                                    })->modalWidth(MaxWidth::TwoExtraLarge)
                                    ->fillForm(function ($record) {
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
                                    })
                            ),
                    ])->columns(1),
                ])->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label(_fields('created_at'))
                                    ->inlineLabel()
                                    ->content(fn ($record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label(_fields('updated_at'))
                                    ->translateLabel()
                                    ->inlineLabel()
                                    ->content(fn ($record): ?string => $record->updated_at?->diffForHumans()),
                            ])->columns(1),
                        Section::make(_fields('additional'))->schema([
                            Forms\Components\TextInput::make('html_id')
                                ->label(_fields('html_id'))
                                ->default('')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('class')
                                ->label(_fields('html_class'))
                                ->default('')
                                ->maxLength(255),
                        ])->collapsible(),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3),
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
