<?php

namespace SmartCms\Core\Admin\Widgets;

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Core\Admin\Resources\ContactFormResource;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Services\TableSchema;

class TopContactForms extends BaseWidget
{
    protected static ?int $sort = 9;

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('Last contact forms');
    }

    public function table(Table $table): Table
    {
        $currentModel = ContactForm::class;

        return $table
            ->searchable(false)
            ->query(function () use ($currentModel) {
                return $currentModel::query()->orderBy('created_at', 'desc')->take(5);
            })
            ->columns([
                TextColumn::make('form.name')
                    ->label(_columns('form_name')),
                TableSchema::getCreatedAt(),
            ])->actions([
                Action::make('View')
                    ->label(__('View'))
                    ->icon('heroicon-o-eye')
                    ->url(function ($record) {
                        return ContactFormResource::getUrl('index');
                    }),
            ])
            ->paginated(false)->defaultPaginationPageOption(5);
    }
}
