<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Table;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Services\Frontend\SectionService;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder;
use SmartCms\Core\Services\TableSchema;

class TemplateSectionResource extends BaseResource
{
    protected static ?string $model = TemplateSection::class;

    protected static ?int $navigationSort = 1;

    public static string $resourceLabel = 'model_template_section';

    public static ?string $resourceGroup = 'design-template';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    protected static function getFormSchema(Form $form): array
    {
        $components = SectionService::make()->getAllSections();

        return [
            Forms\Components\Group::make([
                Forms\Components\Group::make([
                    Forms\Components\Section::make()
                        ->schema([
                            Schema::getName()->suffixAction(Action::make('design')
                                ->label(_fields('design'))->icon('heroicon-o-cog')
                                ->mountUsing(function ($form, $get) {
                                    $form->fill(['design' => $get('design')]);
                                })
                                ->modalWidth(MaxWidth::TwoExtraLarge)
                                ->form(function ($form) use ($components) {
                                    return $form->schema([
                                        Tabs::make()->schema([
                                            Tab::make(_nav('my_sections'))->schema([
                                                ViewField::make('design')
                                                    ->label(_fields('design'))
                                                    ->view('smart_cms::admin.design')
                                                    ->viewData([
                                                        'options' => $components,
                                                    ])
                                                    ->required()
                                                    ->live()
                                                    ->columnSpanFull(),
                                            ]),
                                            Tab::make(_nav('sections_store'))->schema([
                                                Placeholder::make('coming_soon')
                                                    ->label(_nav('coming_soon'))
                                                    ->content(_nav('under_development')),
                                            ]),
                                            Tab::make(_nav('download_section'))->schema([
                                                Placeholder::make('coming_soon')
                                                    ->label(_nav('coming_soon'))
                                                    ->content(_nav('under_development')),
                                            ]),
                                        ]),
                                    ]);
                                })->action(function ($data, $set, Component $component) {
                                    $set('design', $data['design']);
                                    $component->getContainer()->getParentComponent()->getContainer()->getComponent('dynamicTypeFields')->getChildComponentContainer()->fill();
                                })),
                            Hidden::make('design'),
                        ])->columns(1),
                    Section::make(_fields('component_settings'))
                        ->schema(function (Get $get, $set, $record) use ($components): array {
                            $path = $get('design');
                            $placeholder = Placeholder::make('section_is_empty')
                                ->hiddenLabel()
                                ->content(_fields('section_is_empty_content'));
                            if (! $path) {
                                return [
                                    $placeholder,
                                ];
                            }
                            $currentComponent = null;
                            foreach ($components as $name => $component) {
                                if ($name == $path) {
                                    $currentComponent = $component;
                                    break;
                                }
                            }
                            $fields = [];
                            if (! isset($currentComponent['schema']) || empty($currentComponent['schema'])) {
                                return [
                                    $placeholder,
                                ];
                            }
                            foreach ($currentComponent['schema'] as $field) {
                                $field = ArrayToField::make($field, 'value.');
                                $componentField = Builder::make($field);
                                $fields = array_merge($fields, $componentField);
                            }

                            return $fields;
                        })->live()
                        ->columnSpanFull()->key('dynamicTypeFields'),

                ])->columnSpan(['lg' => 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()->hiddenOn('create')
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->inlineLabel()
                                    ->label(_fields('created_at'))
                                    ->content(fn($record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->inlineLabel()
                                    ->label(_fields('_updated_at'))
                                    ->content(fn($record): ?string => $record->updated_at?->diffForHumans()),
                                Schema::getStatus(),
                            ])->columns(1),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3),
        ];

        return [
            Section::make('')->schema([
                Schema::getName(true)->maxLength(255)->suffixAction(Action::make('design')
                    ->label(_fields('design'))->icon('heroicon-o-cog')
                    ->mountUsing(function ($form, $get) {
                        $form->fill(['design' => $get('design')]);
                    })
                    ->form(function ($form) use ($components) {
                        return $form->schema([
                            Tabs::make()->schema([
                                Tab::make(_nav('my_sections'))->schema([
                                    ViewField::make('design')
                                        ->label(_fields('design'))
                                        ->view('smart_cms::admin.design')
                                        ->viewData([
                                            'options' => $components,
                                        ])
                                        ->required()
                                        ->live()
                                        ->columnSpanFull(),
                                ]),
                                Tab::make(_nav('sections_store'))->schema([
                                    Placeholder::make('coming_soon')
                                        ->label(_nav('coming_soon'))
                                        ->content(_nav('under_development')),
                                ]),
                                Tab::make(_nav('download_section'))->schema([
                                    Placeholder::make('coming_soon')
                                        ->label(_nav('coming_soon'))
                                        ->content(_nav('under_development')),
                                ]),
                            ]),
                        ]);
                    })->action(function ($data, $set, $component) {
                        $set('design', $data['design']);
                        $component->getContainer()->getComponent('dynamicTypeFields')->getChildComponentContainer()->fill();
                    })),
                Hidden::make('design'),
                Section::make(_fields('component_settings'))
                    ->schema(function (Get $get, $set, $record) use ($components): array {
                        $path = $get('design');
                        if (! $path) {
                            return [];
                        }
                        $currentComponent = null;
                        foreach ($components as $name => $component) {
                            if ($name == $path) {
                                $currentComponent = $component;
                                break;
                            }
                        }
                        $fields = [];
                        if (! isset($currentComponent['schema'])) {
                            return [];
                        }
                        foreach ($currentComponent['schema'] as $field) {
                            $field = ArrayToField::make($field, 'value.');
                            $componentField = Builder::make($field);
                            $fields = array_merge($fields, $componentField);
                        }

                        return $fields;
                    })->live()
                    ->columnSpanFull()->key('dynamicTypeFields'),
            ]),
        ];
    }

    protected static function getTableColumns(Table $table): array
    {
        return [
            TableSchema::getName(),
            TableSchema::getStatus(),
            TableSchema::getUpdatedAt(),
        ];
    }

    protected static function getResourcePages(): array
    {
        return [
            'index' => Pages\ListTemplateSection::route('/'),
            'create' => Pages\CreateTemplateSection::route('/create'),
            'edit' => Pages\EditTemplateSection::route('/{record}/edit'),
        ];
    }
}
