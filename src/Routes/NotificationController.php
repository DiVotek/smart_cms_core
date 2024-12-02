<?php

namespace SmartCms\Core\Routes;

use Illuminate\Support\Facades\Session;

class NotificationController
{
    public function index()
    {
        $view = view()->exists('templates::'.template().'.notifications') ? 'templates::'.template().'.notifications' : '';
        if ($view) {
            return view($view, ['notifications' => session('notifications', [])]);
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
        $view = view()->exists('templates::'.template().'.notifications') ? 'templates::'.template().'.notifications' : '';
        if ($view) {
            return view($view, ['notifications' => $notifications]);
        }

        return '';
    }
}
