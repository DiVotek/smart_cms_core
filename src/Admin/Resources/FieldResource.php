<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\FieldResource\Pages;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class FieldResource extends BaseResource
{
    protected static ?string $model = Field::class;

    protected static ?int $navigationSort = 1;

    public static string $resourceLabel = 'field';

    public static ?string $resourceGroup = 'modules';

    public static function getFormSchema(Form $form): array
    {
        $options = [
            Forms\Components\TextInput::make('default')
                ->label(_fields('option'))
                ->maxLength(255)
                ->suffixAction(Action::make('translate')
                    ->badge(function ($get) {
                        $counter = 0;
                        foreach (get_active_languages() as $lang) {
                            $placeholder = $get($lang->slug) ?? null;
                            if ($placeholder && strlen($placeholder) > 0) {
                                $counter++;
                            }
                        }

                        return $counter;
                    })
                    ->icon('heroicon-m-language')->action(function ($data, $set) {
                        foreach ($data as $key => $value) {
                            $set($key, $value);
                        }
                    })->fillForm(function ($get) {
                        $data = [];
                        foreach (get_active_languages() as $lang) {
                            $data[$lang->slug] = $get($lang->slug) ?? '';
                        }

                        return $data;
                    })->form(function ($form) {
                        $schema = [];
                        foreach (get_active_languages() as $lang) {
                            $schema[] = TextInput::make($lang->slug)->label(_fields('option').' ('.$lang->name.')')->default('')->maxLength(255);
                        }

                        return $form->schema($schema);
                    })),
        ];
        $hidden = [];
        foreach (get_active_languages() as $lang) {
            $hidden[] = Forms\Components\Hidden::make('data.'.$lang->slug.'.placeholder');
            $hidden[] = Forms\Components\Hidden::make('data.'.$lang->slug.'.description');
            $options[] = Forms\Components\Hidden::make($lang->slug);
        }

        return [
            Forms\Components\Group::make([
                ...$hidden,
                Forms\Components\Group::make([
                    Forms\Components\Section::make()
                        ->schema([
                            Schema::getReactiveName()->suffixActions([
                                Schema::getTranslateAction(),
                            ])->live(onBlur: true)->afterStateUpdated(function ($state, $set, $get) {
                                $placeholder = $get('data.placeholder');
                                $description = $get('data.description');
                                if (str_contains($state, $placeholder)) {
                                    $set('data.placeholder', $state);
                                }
                                if (str_contains($state, $description)) {
                                    $set('data.description', $state);
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
                        ])->columns(2),
                    Forms\Components\Section::make(_fields('options'))->schema([
                        Repeater::make('data.options')->schema($options)
                            ->nullable(),
                    ])->hidden(fn ($get) => ! in_array($get('type'), ['select', 'radio', 'checkbox'])),
                    Forms\Components\Section::make()->schema([
                        Forms\Components\TextInput::make('data.placeholder')
                            ->formatStateUsing(fn (?string $state, $record): string => blank($state) ? $record->name ?? '' : $state)
                            ->label(_fields('placeholder'))
                            ->maxLength(255)
                            ->suffixAction(Action::make('translate')
                                ->badge(function ($get) {
                                    $counter = 0;
                                    foreach (get_active_languages() as $lang) {
                                        $placeholder = $get('data.'.$lang->slug.'.placeholder') ?? null;
                                        if ($placeholder && strlen($placeholder) > 0) {
                                            $counter++;
                                        }
                                    }

                                    return $counter;
                                })
                                ->icon('heroicon-m-language')->action(function ($data, $set) {
                                    foreach ($data as $key => $value) {
                                        $set('data.'.$key.'.placeholder', $value);
                                    }
                                })->fillForm(function ($get) {
                                    $data = [];
                                    foreach (get_active_languages() as $lang) {
                                        $data[$lang->slug] = $get('data.'.$lang->slug.'.placeholder') ?? '';
                                    }

                                    return $data;
                                })->form(function ($form) {
                                    $schema = [];
                                    foreach (get_active_languages() as $lang) {
                                        $schema[] = TextInput::make($lang->slug)->label(_fields('placeholder').' ('.$lang->name.')')->default('')->maxLength(255);
                                    }

                                    return $form->schema($schema);
                                })),
                        Forms\Components\TextInput::make('data.description')
                            ->formatStateUsing(fn (?string $state, $record): string => blank($state) ? $record->name ?? '' : $state)
                            ->label(_fields('description'))
                            ->maxLength(255)
                            ->suffixAction(
                                Action::make('translate')
                                    ->badge(function ($get) {
                                        $counter = 0;
                                        foreach (get_active_languages() as $lang) {
                                            $desc = $get('data.'.$lang->slug.'.description') ?? null;
                                            if ($desc && strlen($desc) > 0) {
                                                $counter++;
                                            }
                                        }

                                        return $counter;
                                    })
                                    ->icon('heroicon-m-language')->action(function ($data, $set) {
                                        foreach ($data as $key => $value) {
                                            $set('data.'.$key.'.description', $value);
                                        }
                                    })->fillForm(function ($get) {
                                        $data = [];
                                        foreach (get_active_languages() as $lang) {
                                            $data[$lang->slug] = $get('data.'.$lang->slug.'.description') ?? '';
                                        }

                                        return $data;
                                    })->form(function ($form) {
                                        $schema = [];
                                        foreach (get_active_languages() as $lang) {
                                            $schema[] = TextInput::make($lang->slug)->label(_fields('description').' ('.$lang->name.')')->default('')->maxLength(255);
                                        }

                                        return $form->schema($schema);
                                    })
                            ),
                        Forms\Components\TextInput::make('data.mask')
                            ->label(_fields('mask'))
                            ->maxLength(255),
                    ]),
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
                                    ->label(_fields('_updated_at'))
                                    ->translateLabel()
                                    ->inlineLabel()
                                    ->content(fn ($record): ?string => $record->updated_at?->diffForHumans()),
                            ])->columns(1),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3),
        ];
    }

    public static function getTableColumns(Table $table): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(_columns('username'))
                ->searchable(),
            Tables\Columns\TextColumn::make('type')
                ->label(_columns('type')),
            TableSchema::getUpdatedAt(),
        ];
    }

    public static function getResourcePages(): array
    {
        return [
            'index' => Pages\ListFields::route('/'),
            'edit' => Pages\EditFields::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
