<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Models\TemplateSection;

class ListTemplateSection extends ListRecords
{
    protected static string $resource = TemplateSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make(_actions('help'))
                ->iconButton()
                ->icon('heroicon-o-question-mark-circle')
                ->modalDescription(_hints('help.page'))
                ->modalFooterActions([]),
            Actions\Action::make(_actions('header_footer'))
                ->label(_actions('header_footer'))
                ->slideOver()
                ->icon('heroicon-o-cog')
                ->fillForm(function (): array {
                    return [
                        'header' => _settings('header', []),
                        'footer' => _settings('footer', []),
                    ];
                })
                ->action(function (array $data): void {
                    setting([
                        sconfig('header') => $data['header'],
                        sconfig('footer') => $data['footer'],
                    ]);
                })
                ->hidden(function () {
                    template() == '';
                })
                ->form(function ($form) {
                    $config = scms_template_config();
                    $theme = $config['theme'] ?? [];
                    $schema = [];
                    foreach ($theme as $key => $value) {
                        $schema[] = ColorPicker::make('theme.'.$key)
                            ->label(ucfirst($key))
                            ->default($value);
                    }

                    return $form
                        ->schema([
                            Section::make('')->schema([
                                Repeater::make('header')->schema([
                                    Select::make('template_section_id')->options(
                                        TemplateSection::query()->where('design', 'like', '%layout%')->pluck('name', 'id')->toArray()
                                    )->label(_fields('template_section')),
                                ]),
                                Repeater::make('footer')->schema([
                                    Select::make('template_section_id')->options(
                                        TemplateSection::query()->where('design', 'like', '%layout%')->pluck('name', 'id')->toArray()
                                    )->label(_fields('template_section')),
                                ]),
                            ]),
                        ]);
                }),
            Actions\CreateAction::make(),
        ];
    }
}
