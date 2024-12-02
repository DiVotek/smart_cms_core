<?php

namespace SmartCms\Core\Services;

use Filament\Notifications\Notification;
use SmartCms\Core\Models\Admin;

class AdminNotification
{
    protected string $title;

    protected int $type;

    public const TYPE_INFO = 1;

    public const TYPE_WARNING = 2;

    public const TYPE_ERROR = 3;

    public const TYPE_SUCCESS = 4;

    public function __construct(string $title = '', int $type = self::TYPE_INFO)
    {
        $this->title = $title;
        $this->type = $type;
    }

    public static function make(): self
    {
        return new self;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function info(): self
    {
        $this->type = self::TYPE_INFO;

        return $this;
    }

    public function warning(): self
    {
        $this->type = self::TYPE_WARNING;

        return $this;
    }

    public function error(): self
    {
        $this->type = self::TYPE_ERROR;

        return $this;
    }

    public function success(): self
    {
        $this->type = self::TYPE_SUCCESS;

        return $this;
    }

    public function send(Admin $admin): void
    {
        $notification = Notification::make()
            ->title($this->title);
        if ($this->type === self::TYPE_INFO) {
            $notification->info();
        } elseif ($this->type === self::TYPE_WARNING) {
            $notification->warning();
        } elseif ($this->type === self::TYPE_ERROR) {
            $notification->error();
        } elseif ($this->type === self::TYPE_SUCCESS) {
            $notification->success();
        }
        $admin->notifyNow($notification->toDatabase());
    }

    public function sendToAll(): void
    {
        foreach (Admin::all() as $admin) {
            $this->send($admin);
        }
    }
}
