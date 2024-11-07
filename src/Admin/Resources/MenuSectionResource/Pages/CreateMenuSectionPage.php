<?php

namespace SmartCms\Core\Admin\Resources\MenuSectionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\MenuSectionResource;
use SmartCms\Core\Models\Page;

class CreateMenuSectionPage extends CreateRecord
{
    protected static string $resource = MenuSectionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);
        $slug = \Illuminate\Support\Str::slug($record->name);
        $parent_id = null;
        if (Page::query()->where('slug', $slug)->exists()) {
            $parent_id = Page::query()->where('slug', $slug)->first()->id;
        } else {
            $page = Page::query()->create([
                'name' => $record->name,
                'slug' => \Illuminate\Support\Str::slug($record->name),
            ]);
            $parent_id = $page->id;
        }
        $record->update([
            'parent_id' => $parent_id,
        ]);

        return $record;
    }
}
