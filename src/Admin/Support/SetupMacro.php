<?php

namespace SmartCms\Core\Admin\Support;

use Filament\Actions\Action;
use Filament\Actions\CreateAction as ActionsCreateAction;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

class SetupMacro extends BaseSetup
{
    public function handle(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table->paginationPageOptions([10, 25, 50, 100, 'all'])->defaultPaginationPageOption(25);
        });
        Field::macro('translatable', function () {
            if (is_multi_lang()) {
                return $this->hint('Translatable')
                    ->hintIcon('heroicon-m-language');
            }

            return $this;
        });
        Form::configureUsing(function (Form $form): void {
            $form->columns(1);
        });
        Select::configureUsing(function (Select $select): void {
            $select->native(false)->preload()->searchable();
        });
        EditAction::configureUsing(function (EditAction $action): void {
            $action->iconButton();
        });
        CreateAction::configureUsing(function (CreateAction $action): void {
            $action->label(_actions('create'))->icon('heroicon-m-plus')->createAnother(false);
        });
        ViewAction::configureUsing(function (ViewAction $action): void {
            $action->iconButton();
        });
        DeleteAction::configureUsing(function (DeleteAction $action): void {
            $action->iconButton();
        });
        DetachAction::configureUsing(function (DetachAction $action): void {
            $action->iconButton();
        });
        ActionsCreateAction::configureUsing(function (ActionsCreateAction $action): void {
            $action->label(_actions('create'))->icon('heroicon-m-plus')->createAnother(false);
        });
        AttachAction::configureUsing(function (AttachAction $action): void {
            $action->attachAnother(false);
        });
        ActionsDeleteAction::configureUsing(function (ActionsDeleteAction $action): void {
            $action->icon('heroicon-o-x-circle');
        });
        Action::macro('iconic', function () {
            return $this->iconButton()
                ->size(ActionSize::ExtraLarge);
        });
        Action::macro('create', function () {
            return $this->label(_actions('create'))
                ->modalSubmitActionLabel(_actions('create'))
                ->icon('heroicon-m-plus');
        });
        Action::macro('settings', function () {
            return $this->label(_actions('settings'))
                ->icon('heroicon-m-cog-6-tooth')
                ->iconic()
                ->iconButton()->color('warning')
                ->tooltip(_actions('settings'));
        });
        Action::macro('template', function () {
            return $this->label(_actions('template'))
                ->icon('heroicon-o-square-3-stack-3d')
                ->iconButton()
                ->tooltip(_actions('template'))
                ->color(Color::Blue);
        });
        Action::macro('help', function (string $description = '') {
            return $this->label(_actions('help'))
                ->icon('heroicon-o-question-mark-circle')
                ->iconic()
                ->modalFooterActions([])
                ->modalDescription($description)
                ->tooltip(_actions('help'));
        });
    }
}
