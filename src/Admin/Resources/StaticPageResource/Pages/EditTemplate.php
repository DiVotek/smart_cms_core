<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Services\TableSchema;

class EditTemplate extends ManageRelatedRecords
{
    protected static string $resource = StaticPageResource::class;

    protected static string $relationship = 'template';

    public static function getNavigationLabel(): string
    {
        return _nav('template');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-m-light-bulb';
    }

    public static function getNavigationBadge(): ?string
    {
        $pageId = request()->route('record', 0);

        return Page::query()->find($pageId)?->template()->count() ?? 0;
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(_fields('component_settings'))
                    ->schema(function (Get $get): array {
                        $fields = [];
                        $sectionId = $get('template_section_id') ?? 0;
                        $section = TemplateSection::find($sectionId);
                        if ($section) {
                            $fields = $section->getFields();
                        }

                        return $fields;
                    })
                    ->columnSpanFull()->key('dynamicTypeFields'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query->withoutGlobalScopes()->orderBy('sorting', 'asc');
            })
            ->recordTitleAttribute('name')
            ->reorderable('sorting')
            ->columns([
                Tables\Columns\TextColumn::make('section.name'),
                ToggleColumn::make('status')->label(_columns('status')),
                TableSchema::getSorting(),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->form([
                    Select::make('sections')
                        ->options(
                            TemplateSection::query()
                                ->where('status', true)
                                ->get()
                                ->mapWithKeys(function ($section) {
                                    return [$section->id => $section->name];
                                })->toArray()
                        )
                        ->createOptionForm(function ($form) {
                            return TemplateSectionResource::form($form);
                        })
                        ->createOptionUsing(function (array $data, string $model): int {
                            $section = TemplateSection::query()->create($data);

                            return $section->getKey();
                        })
                        ->multiple()
                        ->preload()
                        ->native(false)
                        ->searchable()
                        ->label(_fields('component'))
                        ->required()
                        ->live(),
                ])->createAnother(false)
                    ->using(function (array $data, string $model): Model {
                        $newSection = null;
                        $sorting = $model::query()->where('entity_id', $this->record->getKey())
                            ->where('entity_type', $this->record->getMorphClass())
                            ->max('sorting') ?? 0;
                        if (isset($data['sections'])) {
                            foreach ($data['sections'] as $section) {
                                $sorting++;
                                $newSection = $this->record->template()->create([
                                    'template_section_id' => (int) $section,
                                    'sorting' => $sorting,
                                ]);
                            }
                        }

                        return $newSection;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->mutateRecordDataUsing(function (array $data, $record): array {
                    $section = TemplateSection::find($data['template_section_id']);
                    if ($data['value'] == null) {
                        $data['value'] = $section->value ?? [];
                    }

                    return $data;
                })->mutateFormDataUsing(function (array $data, $record): array {
                    $section = TemplateSection::find($record->template_section_id);
                    if (! $section) {
                        return [];
                    }
                    if (empty($data['value'])) {
                        return [];
                    }
                    if ($section->value == $data['value']) {
                        $data['value'] = null;
                    } else {
                        $newSection = $section->replicate();
                        $newSection->value = $data['value'];
                        $newSection->name .= ' - '.$this->record->name;
                        $newSection->save();
                        $record->template_section_id = $newSection->id;
                        $record->save();
                        $data['value'] = null;
                    }

                    return $data;
                }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make(__('Clone'))
                        ->icon('heroicon-o-document-duplicate')
                        ->hidden(function ($record) {
                            return $record->value == null;
                        })
                        ->requiresConfirmation()->form(function ($form) {
                            return $form->schema([
                                TextInput::make('name')->label(_fields('name'))->required(),
                            ]);
                        })->action(function ($record, $data) {
                            $oldSection = TemplateSection::find($record->template_section_id);
                            TemplateSection::query()->create([
                                'name' => $data['name'],
                                'status' => $oldSection->status,
                                'locked' => $oldSection->locked,
                                'design' => $oldSection->design,
                                'schema' => $oldSection->schema,
                                'value' => $record->value,
                                'is_system' => $oldSection->is_system,
                                'template' => $oldSection->template,
                            ]);
                            Notification::make()
                                ->title(__('Record was saved'))
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make(__('Restore'))
                        ->icon('heroicon-o-arrow-path')
                        ->hidden(function ($record) {
                            return $record->value == null;
                        })
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->value = null;
                            $record->save();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->paginated(false);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\ViewAction::make()
                ->url(fn ($record) => $record->route())
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->openUrlInNewTab(true),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function () {
                    $url = ListStaticPages::getUrl();
                    $parent = $this->record->parent;
                    if ($parent) {
                        $menuSection = MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->first();
                        if ($menuSection) {
                            $name = $menuSection->name;
                            if ($parent->parent_id == null && $menuSection->is_categories) {
                                $name = $menuSection->name.'Categories';
                            }
                            $url = ListStaticPages::getUrl([
                                'activeTab' => $name,
                            ]);
                        }
                    }
                    $this->getOwnerRecord()->touch();

                    return redirect()->to($url);
                }),
            \Filament\Actions\Action::make(_actions('save'))
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->getOwnerRecord()->touch();
                })
                ->formId('form'),
        ];
    }
}
