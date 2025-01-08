<?php

namespace SmartCms\Core\Routes;

use Illuminate\Support\Facades\Session;

class NotificationController
{
    public function index()
    {
        $templ = 'templates::'.template().'.notifications';
        if (view()->exists($templ)) {
            return view($templ, ['notifications' => session('notifications', [])]);
        }

        return '';
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
