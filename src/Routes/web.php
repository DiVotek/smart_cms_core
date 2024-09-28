<?php

use Illuminate\Support\Facades\Route;
use SmartCms\Core\Routes\GetRobots;
use SmartCms\Core\Routes\GetSitemap;
use SmartCms\Core\Routes\GetSlug;

Route::get('/robots.txt', GetRobots::class)->name('robots');
Route::get('sitemap.xml', GetSitemap::class)->name('sitemap');

Route::get('/{slug?}', GetSlug::class)->name('slug');

if (is_multi_lang()) {
    foreach (get_active_languages() as $language) {
        Route::get($language->slug.'/{slug?}', GetSlug::class)->name($language->slug.'.slug');
    }
}
