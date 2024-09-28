<?php

namespace SmartCms\Core\Admin\Resources;

use SmartCms\Core\Admin\Resources\TranslationResource\Pages;
use SmartCms\Core\Admin\Resources\TranslationResource\RelationManagers;
use SmartCms\Core\Models\Setting;
use SmartCms\Core\Models\Translation;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('System');
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::query();
        if (!is_multi_lang()) {
            $query->where('language_id', main_lang_id());
        }
        return $query->count();
    }
    public static function form(Form $form): Form
    {
        $language = Hidden::make('language_id');
        if (is_multi_lang()) {
            $language = Schema::getSelect('language_id')->relationship('language', 'name');
        }
        $language = $language->default(main_lang_id());
        return $form
            ->schema([
                Section::make('')->schema([
                    Forms\Components\TextInput::make('key')
                        ->required(),
                    $language,
                    Forms\Components\TextInput::make('value')
                        ->required(),
                ])
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (!is_multi_lang()) {
                    $query->where('language_id', main_lang_id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable(),
                Tables\Columns\TextColumn::make('language.name')
                    ->numeric()
                    ->sortable()->hidden(!is_multi_lang()),
                Tables\Columns\TextInputColumn::make('value')
                    ->searchable(),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([
                SelectFilter::make('language_id')
                    ->relationship('language', 'name')->hidden(!is_multi_lang())
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Schema::helpAction('Translation help'),
                Tables\Actions\Action::make('Clear cache')->action(function () {
                    cache()->forget('translations');
                    Notification::make()
                        ->title(__('Translations cache was cleared'))
                        ->success()
                        ->send();
                }),
                Tables\Actions\Action::make(__('Settings'))
                ->slideOver()
                ->icon('heroicon-o-cog')
                ->fillForm(function (): array {
                    return [
                        'is_translates' => setting(config('settings.add_translations'), []),
                    ];
                })
                ->action(function (array $data): void {
                    setting([
                        config('settings.add_translations') => $data['is_translates'],
                    ]);
                    Setting::updatedSettings();
                })
                ->form(function ($form) {
                    return $form
                        ->schema([
                            Toggle::make('is_translates')
                                ->label(__('Add translations'))
                                ->helperText(__('Enable or disable adding translations from front. This action reduce performance'))
                                ->default(setting(config('settings.add_translations'), [])),
                        ]);
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}
