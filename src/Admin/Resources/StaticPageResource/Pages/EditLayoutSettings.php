<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\Layout;

class EditLayoutSettings extends EditRecord
{
    protected static string $resource = StaticPageResource::class;

    public function getTitle(): string
    {
        return _actions('edit_layout_settings');
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(_fields('layout_settings'))
                    ->label(_fields('layout_settings'))
                    ->headerActions([
                        Action::make('reset')->label(_actions('reset'))
                            ->icon('heroicon-o-arrow-path')
                            ->action(function () {
                                $this->record->update([
                                    'layout_settings' => null,
                                ]);
                                $this->form->fill();
                            })->requiresConfirmation(),
                    ])
                    ->schema($this->record->getLayoutSettingsForm())
                    ->columns(1)
                    ->visible(fn() => $this->record->layout_id !== null),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            Actions\ViewAction::make()
                ->url(fn() => $this->record->route())
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->openUrlInNewTab(true),
            Actions\Action::make('save_close')
                ->label(_actions('save_close'))
                ->icon('heroicon-o-check-badge')
                ->action(function () {
                    $this->save();

                    return redirect()->to(ListStaticPages::getUrl());
                }),
            Actions\Action::make('save')
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->save();
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($this->record->layout && $this->record->layout->value == $data['layout_settings']) {
            return $record;
        }

        $record->update([
            'layout_settings' => $data['layout_settings'] ?? [],
        ]);

        return $record;
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!$this->record->layout || $this->record->layout_settings || !empty($this->record->layout_settings)) {
            return $data;
        }
        $data['layout_settings'] = $this->record->layout->value;
        return $data;
    }
}
