<?php

use Illuminate\Support\Facades\Route;
use SmartCms\Core\Routes\NotificationController;

Route::get('robots.txt', config('shared.routes.robots_handler'))->name('robots');
Route::get('sitemap.xml', config('shared.routes.sitemap_handler'))->name('sitemap');
Route::get('/{lang?}/{slug?}/{second_slug?}/{third_slug?}', config('shared.routes.route_handler'))
    ->where('slug', '^(?!admin|api|login|register|dashboard|glide|_debugbar|.well-known).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->name('smart_cms_page.lang')
    ->middleware('web');
Route::get('/{slug?}/{second_slug?}/{third_slug?}', config('shared.routes.route_handler'))
    ->where('slug', '^(?!admin|api|login|register|dashboard|glide|_debugbar|.well-known).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->middleware('web')
    ->name('smart_cms_page');

Route::get('/api/form', config('shared.routes.form_handler'))->name('smartcms.form.submit')->middleware('web');
Route::get('/api/notifications', [NotificationController::class, 'index'])->name('notifications.list')->middleware('web');
Route::get('/api/notifications/delete/{id}', [NotificationController::class, 'delete'])->name('notifications.delete')->middleware('web');
