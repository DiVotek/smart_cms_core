<?php

namespace SmartCms\Core\Admin\Components;

use Filament\Forms\Components\Placeholder;

class Timestamp
{
    public static function make(string $column, ?string $label = null): Placeholder
    {
        return Placeholder::make($column)
            ->inlineLabel()
            ->label(function () use ($column, $label): ?string {
                if ($label) {
                    return $label;
                }

                return match ($column) {
                    'created_at' => _fields('created_at'),
                    'updated_at' => _fields('_updated_at'),
                    'deleted_at' => _fields('deleted_at'),
                    default => null,
                };
            })
            ->content(fn($record): string => $record?->$column ? $record->$column->diffForHumans() : '-');
    }
}
