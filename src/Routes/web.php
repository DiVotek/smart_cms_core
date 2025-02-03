<?php

use Illuminate\Support\Facades\Route;
use SmartCms\Core\Routes\Handlers\FormHandler;
use SmartCms\Core\Routes\Handlers\PageHandler;
use SmartCms\Core\Routes\Handlers\RobotsHandler;
use SmartCms\Core\Routes\Handlers\SitemapHandler;
use SmartCms\Core\Routes\NotificationController;

Route::get('robots.txt', RobotsHandler::class)->name('robots');
Route::get('sitemap.xml', SitemapHandler::class)->name('sitemap');
Route::get('/{lang?}/{slug?}/{second_slug?}/{third_slug?}', PageHandler::class)
    ->where('slug', '^(?!admin|api|login|register|dashboard|glide|_debugbar|.well-known).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->name('cms.page.lang')
    ->middleware('web');
Route::get('/{slug?}/{second_slug?}/{third_slug?}', PageHandler::class)
    ->where('slug', '^(?!admin|api|login|register|dashboard|glide|_debugbar|.well-known).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->middleware('web')
    ->name('cms.page');
Route::post('/api/form', FormHandler::class)->name('smartcms.form.submit')->middleware('web');
Route::get('/api/notifications', [NotificationController::class, 'index'])->name('notifications.list')->middleware('web');
Route::get('/api/notifications/delete/{id}', [NotificationController::class, 'delete'])->name('notifications.delete')->middleware('web');
