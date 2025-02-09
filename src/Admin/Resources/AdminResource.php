<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\AdminResource\Pages;
use SmartCms\Core\Models\Admin;
use SmartCms\Core\Services\TableSchema;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    public static function getNavigationGroup(): ?string
    {
        return _nav('system');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return _nav('admin');
    }

    public static function canDelete(Model $record): bool
    {
        return Admin::query()->count() > 1;
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('admins');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Forms\Components\TextInput::make('username')
                        ->label(_fields('username'))
                        ->required()->unique(),
                    Forms\Components\TextInput::make('email')
                        ->label(_fields('email'))
                        ->email()->unique()
                        ->required(),
                    Forms\Components\TextInput::make('password')
                        ->label(_fields('password'))
                        ->password()
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label(_columns('username'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(_columns('email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(_columns('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
