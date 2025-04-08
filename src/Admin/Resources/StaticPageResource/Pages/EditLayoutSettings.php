<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Base\Pages\BaseEditRecord;
use SmartCms\Core\Admin\Resources\StaticPageResource;

class EditLayoutSettings extends BaseEditRecord
{
    protected static string $resource = StaticPageResource::class;

    public function getTitle(): string
    {
        return _actions('edit_layout_settings');
    }

    public function form(Form $form): Form
    {
        $schema = $this->record->getLayoutSettingsForm();
        $schema[] = Placeholder::make('layout_settings_placeholder')
            ->hiddenLabel()
            ->content(_actions('empty_layout_settings'))
            ->visible(count($schema) === 0);

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
                    ->schema($schema)
                    ->columns(1)
                    ->visible(fn () => $this->record->layout_id !== null),
            ]);
    }

    protected function getResourceHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->url(fn () => $this->record->route())
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->openUrlInNewTab(true),
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
        if (! $this->record->layout || $this->record->layout_settings || ! empty($this->record->layout_settings)) {
            return $data;
        }
        $data['layout_settings'] = $this->record->layout->value;

        return $data;
    }
}
