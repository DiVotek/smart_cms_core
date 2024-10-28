<?php

namespace SmartCms\Core\Admin\Resources\EmailResource\Pages;


use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use SmartCms\Core\Admin\Resources\EmailResource;

class CreateEmail extends CreateRecord
{
    protected static string $resource = EmailResource::class;
}
