<?php

namespace SmartCms\Core\Admin\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile;
use NotificationChannels\Telegram\TelegramUpdates;
use SmartCms\Core\Traits\HasHooks;

class Profile extends EditProfile
{
    use HasHooks;

    public function form(Form $form): Form
    {
        $tgNotifications = [
            Forms\Components\Toggle::make('notifications.telegram.update')
                ->label(_fields('update'))
                ->default(true),
            Forms\Components\Toggle::make('notifications.telegram.new_contact_form')
                ->label(_fields('new_contact_form'))
                ->default(true),
        ];
        static::applyHook('telegram.notifications', $tgNotifications, 'telegram');
        $mailNotifications = [
            Forms\Components\Toggle::make('notifications.mail.update')
                ->label(_fields('update'))
                ->default(true),
            Forms\Components\Toggle::make('notifications.mail.new_contact_form')
                ->label(_fields('new_contact_form'))
                ->default(true),
        ];
        static::applyHook('mail.notifications', $mailNotifications, 'mail');

        return $form->schema([
            TextInput::make('username')
                ->label(_fields('username'))
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            TextInput::make('telegram_token')->disabled()->hidden()->formatStateUsing(function ($get) {
                return \Illuminate\Support\Str::random(32);
            }),
            TextInput::make('telegram_id')
                ->label(_fields('telegram_chat_id'))
                ->suffixActions(
                    [
                        Action::make('copy_telegram_link')
                            ->label(_fields('copy_telegram_link'))
                            ->icon('heroicon-o-link')
                            ->url(function ($get) {
                                $token = $get('telegram_token');
                                $botUsername = _settings('telegram.bot_username');
                                $url = "https://t.me/{$botUsername}?start={$token}";

                                return $url;
                            })
                            ->openUrlInNewTab(),
                        Action::make('get_telegram_id')
                            ->label(_fields('get_telegram_id'))
                            ->action(function ($set, $get) {
                                $token = $get('telegram_token');
                                $updates = TelegramUpdates::create()
                                    ->latest()
                                    ->limit(5)
                                    ->options([
                                        'timeout' => 0,
                                    ])
                                    ->get();
                                if ($updates['ok']) {
                                    $messages = $updates['result'];
                                    foreach ($messages as $message) {
                                        if (! isset($message['message']['text'])) {
                                            continue;
                                        }
                                        $text = $message['message']['text'];
                                        if ($text == '/start '.$token) {
                                            $chatId = $message['message']['chat']['id'];
                                            $set('telegram_id', $chatId);
                                            break;
                                        }
                                    }
                                }
                            })
                            ->openUrlInNewTab()
                            ->icon('heroicon-o-arrow-path')
                            ->color('success'),
                    ]
                )->readOnly(),
            Forms\Components\Section::make(_fields('mail_notifications'))->schema($mailNotifications)->columns(2),
            Forms\Components\Section::make(_fields('telegram_notifications'))->schema($tgNotifications)->columns(2),
        ]);
    }
}
