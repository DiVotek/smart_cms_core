<?php

namespace SmartCms\Core\Services;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;

class TableSchema
{
    public static function getCreatedAt(): TextColumn
    {
        return TextColumn::make('created_at')
            ->label(_fields('created_at'))
            ->since()
            ->toggleable()
            ->sortable();
    }

    public static function getUpdatedAt(): TextColumn
    {
        return TextColumn::make('updated_at')
            ->label(_fields('_updated_at'))
            ->since()
            ->toggleable()
            ->sortable();
    }

    public static function getName(): TextColumn
    {
        return TextColumn::make('name')
            ->label(_columns('name'))
            ->searchable();
    }

    public static function getStatus(): ToggleColumn
    {
        return ToggleColumn::make('status')
            ->label(_columns('status'))
            ->sortable();
    }

    public static function getViews(): TextColumn
    {
        return TextColumn::make('views')
            ->label(_columns('views'))
            ->badge()
            ->sortable()
            ->toggleable()
            ->numeric();
    }

    public static function getSorting(): TextColumn
    {
        return TextColumn::make('sorting')
            ->label(_columns('sorting'))
            ->toggleable()
            ->badge()
            ->color('gray')
            ->sortable();
    }

    public static function getImage(): ImageColumn
    {
        return ImageColumn::make('image')
            ->label(_columns('image'));
    }

    public static function getFilterStatus(): SelectFilter
    {
        return SelectFilter::make('status')
            ->label(_columns('status'))
            ->options([
                Status::ON => __('On'),
                Status::OFF => __('Off'),
            ]);
    }
}
