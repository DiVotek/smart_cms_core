<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\Page;

class ListStaticPages extends ListRecords
{
    protected static string $resource = StaticPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')->modifyQueryUsing(fn (Builder $query) => $query->whereNull('parent_id')),
        ];
        foreach (Page::query()->where('is_nav', true)->get() as $page) {
            $tabs[$page->name()] = Tab::make($page->name())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('parent_id', $page->id));
        }

        return $tabs;
    }
}
