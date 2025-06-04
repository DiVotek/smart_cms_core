<?php

namespace SmartCms\Core\Admin\Resources;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use SmartCms\Core\Admin\Resources\EmailResource\Pages;
use SmartCms\Core\Models\Email;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class EmailResource extends Resource
{
    protected static ?string $model = Email::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationGroup(): ?string
    {
        return _nav('communication');
    }

    public static function getModelLabel(): string
    {
        return _nav('email');
    }

    public static function getPluralModelLabel(): string
    {
        return _nav('emails');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Schema::getName(true),
                    Forms\Components\TextInput::make('subject')
                        ->label(_fields('subject'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\RichEditor::make('content')
                        ->label(_fields('content'))
                        ->required()
                        ->columnSpanFull(),
                    TagsInput::make('users')->label(_fields('users'))->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TableSchema::getName(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_sent')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TableSchema::getUpdatedAt(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Send')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->requiresConfirmation()->action(function (Email $email) {
                        config([
                            'mail.mailers.smtp.transport' => 'smtp',
                            'mail.mailers.smtp.host' => _settings('mailer.host'),
                            'mail.mailers.smtp.port' => _settings('mailer.port'),
                            'mail.mailers.smtp.username' => _settings('mailer.username'),
                            'mail.mailers.smtp.password' => _settings('mailer.password'),
                            'mail.mailers.smtp.encryption' => _settings('mailer.encryption'),
                            'mail.from.address' => _settings('mailer.from'),
                            'mail.from.name' => _settings('mailer.name'),
                        ]);
                        $users = $email->users;
                        // foreach ($users as $user) {
                        //     Mail::mailer('smtp')->to($user)->send(new DefaultEmail($email));
                        // }
                        $email->update(['is_sent' => true]);
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmails::route('/'),
            'create' => Pages\CreateEmail::route('/create'),
            'edit' => Pages\EditEmail::route('/{record}/edit'),
        ];
    }
}
