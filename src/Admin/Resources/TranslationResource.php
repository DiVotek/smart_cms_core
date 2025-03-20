<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\TranslationResource\Pages;
use SmartCms\Core\Models\Translation;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class TranslationResource extends BaseResource
{
    protected static ?string $model = Translation::class;

    protected static ?int $navigationSort = 4;

    public static string $resourceLabel = 'model_translation';

    public static ?string $resourceGroup = 'system';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::query();
        if (! is_multi_lang()) {
            $query->where('language_id', main_lang_id());
        }

        return $query->count();
    }

    public static function getFormSchema(Form $form): array
    {
        $language = Hidden::make('language_id');
        if (is_multi_lang()) {
            $language = Schema::getSelect('language_id')->relationship('language', 'name')->label(_fields('language'))->disabled();
        }
        $language = $language->default(main_lang_id());

        return [
            Forms\Components\TextInput::make('key')
                ->label(_fields('key'))
                ->required()->disabled(),
            $language,
            Forms\Components\TextInput::make('value')
                ->label(_fields('value'))
                ->required(),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (! is_multi_lang()) {
            $query->where('language_id', main_lang_id());
        }

        return $query;
    }

    public static function getTableColumns(Table $table): array
    {
        return [
            Tables\Columns\TextColumn::make('key')
                ->label(_columns('key'))
                ->searchable(),
            Tables\Columns\TextColumn::make('language.name')
                ->numeric()
                ->label(_columns('language'))
                ->sortable()->hidden(! is_multi_lang()),
            Tables\Columns\TextInputColumn::make('value')
                ->label(_columns('value'))
                ->searchable(),
            TableSchema::getUpdatedAt(),
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            SelectFilter::make('language_id')
                ->relationship('language', 'name')->hidden(! is_multi_lang()),
        ];
    }

    public static function getResourcePages(): array
    {
        return [
            'index' => Pages\ManageTranslations::route('/'),
        ];
    }
}
