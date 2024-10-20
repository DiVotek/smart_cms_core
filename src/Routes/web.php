<?php

use Illuminate\Support\Facades\Route;
use SmartCms\Core\Routes\GetForm;
use SmartCms\Core\Routes\GetRobots;
use SmartCms\Core\Routes\GetSitemap;
use SmartCms\Core\Routes\GetSlug;

Route::get('robots.txt', GetRobots::class)->name('robots');
Route::get('sitemap.xml', GetSitemap::class)->name('sitemap');
Route::get('/{lang?}/{slug?}/{second_slug?}/{third_slug?}', GetSlug::class)
    ->where('slug', '^(?!admin|api|login|register|dashboard).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->name('smart_cms_page.lang');
Route::get('/{slug?}/{second_slug?}/{third_slug?}', GetSlug::class)
    ->where('slug', '^(?!admin|api|login|register|dashboard).*$')
    ->where('lang', '[a-zA-Z]{2}')
    ->name('smart_cms_page');

Route::get('/api/form', GetForm::class)->name('smartcms.form.submit');
