<?php

namespace SmartCms\Core\Admin\Widgets;

use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\TableSchema;

class TopStaticPages extends BaseWidget
{
    protected static ?int $sort = 8;

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('Top static pages');
    }

    public function table(Table $table): Table
    {
        $currentModel = Page::class;

        return $table
            ->searchable(false)
            ->query(function () use ($currentModel) {
                return $currentModel::query()->orderBy('views', 'desc')->take(5);
            })
            ->columns([
                TableSchema::getName(),
                TableSchema::getViews(),
            ])->actions([
                Action::make('View')
                    ->label(__('View'))
                    ->icon('heroicon-o-eye')
                    ->url(function ($record) {
                        return $record->route();
                    }),
            ])
            ->paginated(false)->defaultPaginationPageOption(5);
    }
}
