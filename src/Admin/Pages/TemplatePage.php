<?php

namespace SmartCms\Core\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class TemplatePage extends Page
{
   protected static ?string $slug = 'templates';

   public function getView(): string
   {
      return 'smart_cms::admin.templates';
   }

   public static function getNavigationGroup(): ?string
   {
      return _nav('design-template');
   }

   public function getTitle(): string | Htmlable
   {
      return _nav('templates');
   }

   public static function getNavigationLabel(): string
   {
      return _nav('templates');
   }
}
