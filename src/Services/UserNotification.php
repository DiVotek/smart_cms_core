<?php

namespace SmartCms\Core\Services;

use Illuminate\Support\Facades\Session;

class UserNotification
{
    protected string $title;

    protected int $type;

    protected string $message;

    public const TYPE_INFO = 1;

    public const TYPE_WARNING = 2;

    public const TYPE_ERROR = 3;

    public const TYPE_SUCCESS = 4;

    public function __construct(string $title = '', string $message = '', int $type = self::TYPE_INFO)
    {
        $this->title = $title;
        $this->message = $message;
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

    public function message(string $message): self
    {
        $this->message = $message;

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

   public function send(): void
   {
      $notifications = Session::get('notifications', []);
      $notifications[] = [
         'title' => $this->title,
         'message' => $this->message,
         'type' => $this->type,
         'id' => uniqid()
      ];
      Session::put('notifications', $notifications);
   }
}
