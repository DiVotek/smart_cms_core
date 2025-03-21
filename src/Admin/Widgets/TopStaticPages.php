<?php

namespace SmartCms\Core\Admin\Widgets;

use Filament\Tables\Columns\TextColumn;
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
        return $table
            ->recordUrl(fn ($record) => $record->route())
            ->searchable(false)
            ->query(function () {
                return Page::query()->orderBy('views', 'desc')->take(5);
            })
            ->columns([
                TableSchema::getName(),
                TextColumn::make('views')
                    ->label(_columns('views'))
                    ->badge()->numeric(),
            ])
            ->defaultPaginationPageOption(5);
    }
}
