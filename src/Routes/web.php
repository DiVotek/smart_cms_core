<?php

use Illuminate\Support\Facades\Route;
use SmartCms\Core\Routes\Handlers\FormFieldsHandler;
use SmartCms\Core\Routes\Handlers\FormHandler;
use SmartCms\Core\Routes\Handlers\PageHandler;
use SmartCms\Core\Routes\Handlers\RobotsHandler;
use SmartCms\Core\Routes\Handlers\SitemapHandler;
use SmartCms\Core\Routes\NotificationController;

Route::get('robots.txt', RobotsHandler::class)->name('robots');
Route::get('sitemap.xml', SitemapHandler::class)->name('sitemap');
Route::get('sitemap/{lang?}.xml', SitemapHandler::class)->name('sitemap.lang');
Route::get('/{lang?}/{slug?}/{second_slug?}/{third_slug?}', PageHandler::class)
    ->where('slug', '^(?!admin|api|login|register|dashboard|glide|_debugbar|.well-known).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->name('cms.page.lang')
    ->middleware(['web', 'html.minifier']);
Route::get('/{slug?}/{second_slug?}/{third_slug?}', PageHandler::class)
    ->where('slug', '^(?!admin|api|login|register|dashboard|glide|_debugbar|.well-known).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->middleware(['web', 'html.minifier'])
    ->name('cms.page');
Route::group(['middleware' => ['web', 'lang'], 'prefix' => 'api'], function () {
    Route::post('/form', FormHandler::class)->name('smartcms.form.submit');
    Route::get('/form/fields', FormFieldsHandler::class)->name('smartcms.form.fields');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.list');
    Route::get('/notifications/delete/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');
});
