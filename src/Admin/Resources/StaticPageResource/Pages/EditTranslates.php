<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\Language;

class EditTranslates extends ManageRelatedRecords
{
    protected static string $resource = StaticPageResource::class;

    protected static string $relationship = 'translatable';

    public static function getNavigationLabel(): string
    {
        return _nav('translates');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-language';
    }

    public function getTitle(): string|Htmlable
    {
        $recordTitle = $this->getRecordTitle();

        $recordTitle = $recordTitle instanceof Htmlable ? $recordTitle->toHtml() : $recordTitle;

        return _nav('edit')." {$recordTitle} ".$this->record->name;
    }

    public function form(Form $form): Form
    {
        $languages = $this->record->translatable()->pluck('language_id')->toArray();
        $languages = array_diff(get_active_languages()->pluck('id')->toArray(), $languages);

        return $form
            ->schema([
                TextInput::make('value')
                    ->label(_fields('name'))
                    ->required()
                    ->maxLength(255),
                Select::make('language_id')
                    ->label(_fields('language'))
                    ->hiddenOn('edit')
                    ->disabledOn('edit')
                    ->options(Language::query()->whereIn('id', $languages)->pluck('name', 'id')->toArray())->native(false)->default(function () use ($languages) {
                        return array_shift($languages);
                    }),
            ])->columns(1);
    }

    public static function canViewForRecord(): bool
    {
        return is_multi_lang();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('value')
            ->columns([
                Tables\Columns\TextColumn::make('language.name')->label(_columns('language')),
                Tables\Columns\TextColumn::make('value')->label(_columns('translate')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->hidden(function () {
                    $languages = get_active_languages()->pluck('id')->toArray();
                    $translatedLanguages = $this->record->translatable()->pluck('language_id')->toArray();

                    return ! array_diff($languages, $translatedLanguages);
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->paginated(false);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }
}
