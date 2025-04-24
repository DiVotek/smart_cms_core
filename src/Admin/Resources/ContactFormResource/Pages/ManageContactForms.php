<?php

namespace SmartCms\Core\Admin\Resources\ContactFormResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use SmartCms\Core\Admin\Base\Pages\BaseManageRecords;
use SmartCms\Core\Admin\Resources\ContactFormResource;

class ManageContactForms extends BaseManageRecords
{
    protected static string $resource = ContactFormResource::class;

    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()->orderBy('created_at', 'desc');
    }
}
