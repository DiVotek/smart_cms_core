<?php

namespace SmartCms\Core\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;
use SmartCms\Core\Admin\Resources\ContactFormResource;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\Form;

class NewContactFormNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public ContactForm $form) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = [];
        $notificationSettings = $notifiable->notifications ?? [];
        if (isset($notificationSettings['mail']) && isset($notificationSettings['mail']['new_contact_form']) && $notificationSettings['mail']['new_contact_form']) {
            $via[] = 'mail';
        }
        if (isset($notificationSettings['telegram']) && isset($notificationSettings['telegram']['new_contact_form']) && $notificationSettings['telegram']['new_contact_form'] && $notifiable->telegram_id) {
            $via[] = 'telegram';
        }
        return $via;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $formName = $this->form->form?->name ?? 'Unknown';
        $message = (new MailMessage)
            ->line("🔔 New contact form submission!")
            ->line("Form: {$formName}");

        if ($this->form->data && is_array($this->form->data)) {
            foreach ($this->form->data as $key => $value) {
                $key = ucfirst(str_replace('_', ' ', $key));
                $value = is_array($value) ? implode(', ', $value) : $value;
                $message->line("{$key}: {$value}");
            }
        }
        return $message->action('View in admin panel', ContactFormResource::getUrl())->line('Thank you for using our application!');
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        $formName = $this->form->form?->name ?? 'Unknown';
        return TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content("🔔 New contact form submission!\n\nForm: {$formName}")
            ->button('View in admin panel', ContactFormResource::getUrl());
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
