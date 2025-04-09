<?php

namespace SmartCms\Core\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class UpdateNotification extends Notification
{
    public string $version;

    public string $url;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->version = _settings('version', 0);
        $this->url = url('/admin');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = [];
        $notificationSettings = $notifiable->notifications ?? [];
        if (isset($notificationSettings['mail']) && isset($notificationSettings['mail']['update']) && $notificationSettings['mail']['update']) {
            $via[] = 'mail';
        }
        if (isset($notificationSettings['telegram']) && isset($notificationSettings['telegram']['update']) && $notificationSettings['telegram']['update'] && $notifiable->telegram_id) {
            $via[] = 'telegram';
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('🔔 New CMS update available!')
            ->line(" Version: {$this->version}")
            ->action('Check admin panel for details.', $this->url)
            ->line('Thank you for using our application!');
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content("🔔 New CMS update available!\n\nVersion: {$this->version}")
            ->button('Check admin panel', $this->url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
