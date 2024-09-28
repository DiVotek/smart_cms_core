<?php

namespace SmartCms\Core\Admin\Resources\TranslationResource\Pages;

use SmartCms\Core\Admin\Resources\TranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTranslation extends CreateRecord
{
    protected static string $resource = TranslationResource::class;
}
