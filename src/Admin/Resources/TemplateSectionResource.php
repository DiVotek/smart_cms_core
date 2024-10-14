<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Services\Helper;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class TemplateSectionResource extends Resource
{
    protected static ?string $model = TemplateSection::class;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('design-template');
    }

    public static function getModelLabel(): string
    {
        return _nav('model_template_section');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('model_template_section');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()->withoutGlobalScopes()->count();
    }

    public static function canDelete(Model $record): bool
    {
        return ! $record->is_system;
    }

    public static function canEdit(Model $record): bool
    {
        return ! $record->is_system;
    }

    public static function form(Form $form): Form
    {
        $components = Helper::getComponents();

        return $form
            ->schema([
                Section::make('')->schema([
                    Schema::getName(true)->maxLength(255),
                    // Fieldset::make()->schema([
                    //     Schema::getStatus(),
                    //     Forms\Components\Toggle::make('locked')
                    //         ->required(),
                    // ])->columns(2),
                    Radio::make('design')
                        ->label(_fields('design'))
                        ->options($components)
                        ->required()
                        ->afterStateUpdated(fn(Radio $component) => $component
                            ->getContainer()
                            ->getComponent('dynamicTypeFields')
                            ->getChildComponentContainer()
                            ->fill())->live(),
                    Section::make(_fields('component_settings'))
                        ->schema(function (Get $get): array {
                            $class = $get('design');
                            if (! $class) {
                                return [];
                            }

                            return Helper::getComponentClass($class);
                        })->live()
                        ->columnSpanFull()->key('dynamicTypeFields'),
                ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableSchema::getName(),
                TableSchema::getStatus(),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Schema::helpAction('TemplateSection help'),
                Tables\Actions\Action::make(_actions('header_footer'))
                    ->label(_actions('header_footer'))
                    ->slideOver()
                    ->icon('heroicon-o-cog')
                    ->fillForm(function (): array {
                        return [
                            'header' => _settings('header', []),
                            'footer' => _settings('footer', []),
                        ];
                    })
                    ->action(function (array $data): void {
                        setting([
                            sconfig('header') => $data['header'],
                            sconfig('footer') => $data['footer'],
                        ]);
                    })
                    ->hidden(function () {
                        template() == '';
                    })
                    ->form(function ($form) {
                        $config = scms_template_config();
                        $theme = $config['theme'] ?? [];
                        $schema = [];
                        foreach ($theme as $key => $value) {
                            $schema[] = ColorPicker::make('theme.' . $key)
                                ->label(ucfirst($key))
                                ->default($value);
                        }

                        return $form
                            ->schema([
                                Section::make('')->schema([
                                    Repeater::make('header')->schema([
                                        Select::make('template_section_id')->options(
                                            TemplateSection::query()->where('design', 'like', '%layout%')->pluck('name', 'id')->toArray()
                                        )->label(_fields('template_section')),
                                    ]),
                                    Repeater::make('footer')->schema([
                                        Select::make('template_section_id')->options(
                                            TemplateSection::query()->where('design', 'like', '%layout%')->pluck('name', 'id')->toArray()
                                        )->label(_fields('template_section')),
                                    ]),
                                ]),
                            ]);
                    }),
                Tables\Actions\Action::make(_actions('default_variables'))
                    ->label(_actions('default_variables'))
                    ->slideOver()
                    ->icon('heroicon-o-cog')
                    ->fillForm(function (): array {
                        return [
                            'default_variables' => _settings('default_variables', []),
                        ];
                    })
                    ->action(function (array $data): void {
                        setting([
                            sconfig('default_variables') => $data['default_variables'] ?? [],
                        ]);
                    })
                    ->hidden(function () {
                        if (template() == '') {
                            return true;
                        }
                        if (! isset(scms_template_config()['defaultVariables'])) {
                            return true;
                        }
                        if (empty(scms_template_config()['defaultVariables'])) {
                            return true;
                        }

                        return false;
                    })
                    ->form(function ($form) {
                        $config = scms_template_config();
                        $variables = $config['defaultVariables'] ?? [];
                        if (empty($variables)) {
                            $schema = [];
                        } else {
                            $schema = Helper::parseSchema($variables, 'default_variables.');
                        }

                        return $form->schema($schema);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplateSection::route('/'),
            'create' => Pages\CreateTemplateSection::route('/create'),
            'edit' => Pages\EditTemplateSection::route('/{record}/edit'),
        ];
    }
}
