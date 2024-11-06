<?php

use Illuminate\Support\Facades\Route;

Route::get('robots.txt', config('shared.routes.robots_handler'))->name('robots');
Route::get('sitemap.xml', config('shared.routes.sitemap_handler'))->name('sitemap');
Route::get('/{lang?}/{slug?}/{second_slug?}/{third_slug?}', config('shared.routes.route_handler'))
    ->where('slug', '^(?!admin|api|login|register|dashboard|glide|_debugbar).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->name('smart_cms_page.lang');
Route::get('/{slug?}/{second_slug?}/{third_slug?}', config('shared.routes.route_handler'))
    ->where('slug', '^(?!admin|api|login|register|dashboard|glide|_debugbar).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->name('smart_cms_page');

Route::get('/api/form', config('shared.routes.form_handler'))->name('smartcms.form.submit');
