<?php

namespace SmartCms\Core\Admin\Resources\FieldResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\FieldResource;

class CreateField extends CreateRecord
{
    protected static string $resource = FieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        if (! isset($data['html_id'])) {
            $data['html_id'] = \Illuminate\Support\Str::slug($data['name']) . '-' . \Illuminate\Support\Str::random(5);
        }

        return parent::handleRecordCreation($data);
    }
}
