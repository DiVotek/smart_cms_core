<?php

namespace SmartCms\Core\Admin\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile;

class Profile extends EditProfile
{
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('username')
                ->label(_fields('username'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),

        ]);
    }
}
