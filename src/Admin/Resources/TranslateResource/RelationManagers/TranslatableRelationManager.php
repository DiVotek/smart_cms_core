<?php

namespace SmartCms\Core\Admin\Resources\TranslateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Models\Language;

class TranslatableRelationManager extends RelationManager
{
    protected static string $relationship = 'translatable';

    protected static ?string $title = 'Translates';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Translates');
    }

    public function form(Form $form): Form
    {
        $languages = $this->ownerRecord->translatable()->pluck('language_id')->toArray();
        $languages = array_diff(Language::query()->pluck('id')->toArray(), $languages);

        return $form
            ->schema([
                Forms\Components\TextInput::make('value')
                    ->label(_fields('name'))
                    ->required()
                    ->maxLength(255),
                Select::make('language_id')
                    ->label(_fields('language'))
                    ->hiddenOn('edit')
                    ->disabledOn('edit')
                    ->options(Language::query()->whereIn('id', $languages)->pluck('name', 'id')->toArray())->native(false)
                    ->default($languages[(string) array_key_first($languages)] ?? ''),
            ])->columns(1);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return is_multi_lang();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                Tables\Columns\TextColumn::make('value')->label(_columns('name')),
                Tables\Columns\TextColumn::make('language.name')->label(_columns('language')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->hidden(function () {
                    $languages = Language::query()->pluck('id')->toArray();
                    $translatedLanguages = $this->ownerRecord->translatable()->pluck('language_id')->toArray();

                    return ! array_diff($languages, $translatedLanguages);
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->paginated(false);
    }
}
