<?php

namespace SmartCms\Core\Routes;

use Illuminate\Support\Facades\Session;
use SmartCms\Core\Services\ScmsResponse;

class NotificationController
{
    public function index()
    {
        return new ScmsResponse(true, ['notifications' => session('notifications', [])]);
    }

    public function delete($id)
    {
        $notifications = Session::get('notifications', []);
        $notifications = array_filter($notifications, function ($notification) use ($id) {
            return $notification['id'] != $id;
        });
        Session::put('notifications', $notifications);

        return $this->index();
    }
}
