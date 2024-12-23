<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Core\Admin\Resources\SeoResource;
use SmartCms\Core\Admin\Resources\StaticPageResource;

class EditSeo extends ManageRelatedRecords
{
    protected static string $resource = StaticPageResource::class;

    protected static string $relationship = 'seo';

    public static function getNavigationLabel(): string
    {
        return _nav('seo');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return 'heroicon-o-globe-alt';
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function form(Form $form): Form
    {
        return SeoResource::form($form);
    }

    public function table(Table $table): Table
    {
        return SeoResource::table($table)
            ->modifyQueryUsing(function ($query) {
                if (! is_multi_lang()) {
                    $query->where('language_id', main_lang_id());
                }
            })
            ->headerActions([
                Tables\Actions\CreateAction::make()->hidden(function () {
                    if (is_multi_lang()) {
                        return $this->record->seo()->count() >= count(get_active_languages());
                    }

                    return $this->record->seo !== null;
                }),
            ])->paginated(false);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\ViewAction::make()
                ->url(fn($record) => $record->route())
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->openUrlInNewTab(true),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function ($record, $data) {
                    $this->getOwnerRecord()->touch();
                    return redirect()->to(ListStaticPages::getUrl());
                }),
            \Filament\Actions\Action::make(_actions('save'))
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->getOwnerRecord()->touch();
                })
                ->formId('form'),
        ];
    }
}
