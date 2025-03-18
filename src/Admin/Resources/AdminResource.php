<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use SmartCms\Core\Admin\Base\BaseResource;
use SmartCms\Core\Admin\Resources\AdminResource\Pages;
use SmartCms\Core\Models\Admin;
use SmartCms\Core\Services\TableSchema;

class AdminResource extends BaseResource
{
    protected static ?string $model = Admin::class;

    protected static ?int $navigationSort = 1;

    public static string $resourceLabel = 'admin';

    public static ?string $resourceGroup = 'system';

    public static function canAccess(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return Auth::user()->username == 'superadmin';
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->username == 'superadmin' && $record->username !== 'superadmin';
    }

    public static function canDelete(Model $record): bool
    {
        return $record->username !== 'superadmin';
    }

    public static function getFormSchema(Form $form): array
    {
        return [
            Forms\Components\TextInput::make('username')
                ->label(_fields('username'))
                ->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('email')
                ->label(_fields('email'))
                ->email()->unique(ignoreRecord: true)
                ->required(),
            Forms\Components\TextInput::make('password')
                ->label(_fields('password'))
                ->password()
                ->required(),
        ];
    }

    public static function getTableColumns(Table $table): array
    {
        return [
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
        ];
    }

    public static function getResourcePages(): array
    {
        return [
            'index' => Pages\ManageAdmins::route('/'),
        ];
    }
}
