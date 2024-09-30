<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;
use SmartCms\Core\Admin\Resources\StaticPageResource\Pages as Pages;

class StaticPageResource extends Resource
{
    protected static ?string $model = Page::class;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('pages');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function getModelLabel(): string
    {
        return _nav('page');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('pages');
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Schema::getReactiveName(),
                        Schema::getSlug(),
                        Schema::getSorting(),
                        Schema::getImage(),
                        Schema::getStatus(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableSchema::getName(),
                TableSchema::getStatus(),
                TableSchema::getSorting(),
                TableSchema::getViews(),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([
                TableSchema::getFilterStatus(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('View')
                    ->label(_actions('view'))
                    ->icon('heroicon-o-eye')
                    ->url(function ($record) {
                        return '/' . $record->slug;
                    })->openUrlInNewTab(),
            ])
            ->reorderable('sorting')
            ->headerActions([
                Schema::helpAction('Static page help text'),
                Tables\Actions\Action::make('Template')
                    ->slideOver()
                    ->icon('heroicon-o-cog')
                    ->fillForm(function (): array {
                        return [
                            'template' => setting(config('settings.static_page.template'), []),
                            'design' => setting(config('settings.static_page.design'), 'default'),
                        ];
                    })
                    ->action(function (array $data): void {
                        setting([
                            config('settings.static_page.template') => $data['template'],
                            config('settings.static_page.design') => $data['design'],
                        ]);
                    })
                    ->form(function ($form) {
                        return $form
                            ->schema([
                                Section::make('')->schema([
                                    Schema::getTemplateBuilder()->label(_fields('template')),
                                ]),
                            ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [Schema::getSeoAndTemplateRelationGroup()];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaticPages::route('/'),
            'create' => Pages\CreateStaticPage::route('/create'),
            'edit' => Pages\EditStaticPage::route('/{record}/edit'),
        ];
    }
}
