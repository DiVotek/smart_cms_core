<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use SmartCms\Core\Admin\Resources\SeoResource;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Seo;
use SmartCms\Core\Services\Schema;
use SmartCms\Core\Services\TableSchema;

class EditSeo extends ManageRelatedRecords
{
    protected static string $resource = StaticPageResource::class;

    protected static string $relationship = 'seo';

    public static function getNavigationLabel(): string
    {
        return _nav('seo');
    }

    public static function getNavigationBadge(): ?string
    {
        $pageId = request()->route('record', 0);
        return Page::query()->find($pageId)?->seo()->count() ?? 0;
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
        $lang = [];
        if (is_multi_lang()) {
            $lang = [
                TextColumn::make('language.name')->label(_columns('language'))->toggleable(),
            ];
        }
        $columns = [
            TextColumn::make('title')->label(_fields('seo_title'))->limit(50)->tooltip(function (TextColumn $column): ?string {
                $state = $column->getState();
                if (strlen($state) <= $column->getCharacterLimit()) {
                    return null;
                }

                return $state;
            })->toggleable(),
            ...$lang,
            IconColumn::make('heading')->label(_fields('seo_heading'))->boolean()->toggleable(),
            IconColumn::make('description')->label(_fields('seo_description'))->boolean()->toggleable()->default(false),
            IconColumn::make('summary')->label(_fields('seo_summary'))->toggleable()->boolean(),
            IconColumn::make('content')->label(_fields('seo_content'))->boolean()->toggleable(),
            TableSchema::getUpdatedAt(),
        ];

        return SeoResource::table($table)
            ->modifyQueryUsing(function ($query) {
                if (! is_multi_lang()) {
                    $query->where('language_id', main_lang_id());
                } else {
                    $ids = [main_lang_id()];
                    $ids = array_merge($ids, _settings('additional_languages', []));
                    $query->whereIn('language_id', $ids)->orderByRaw('FIELD(language_id, ' . implode(',', $ids) . ')');
                }
            })
            ->columns($columns)
            ->headerActions([
                Tables\Actions\CreateAction::make()->hidden(function () {
                    if (is_multi_lang()) {
                        return $this->record->seo()->count() >= count(get_active_languages());
                    }

                    return $this->record->seo !== null;
                })->form(Schema::getSeoForm($this->getOwnerRecord()->seo()->pluck('language_id')->toArray())),
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
                ->action(function () {
                    $url = ListStaticPages::getUrl();
                    $parent = $this->record->parent;
                    if ($parent) {
                        $menuSection = MenuSection::query()->where('parent_id', $parent->parent_id ?? $parent->id)->first();
                        if ($menuSection) {
                            $name = $menuSection->name;
                            if ($parent->parent_id == null && $menuSection->is_categories) {
                                $name = $menuSection->name . 'Categories';
                            }
                            $url = ListStaticPages::getUrl([
                                'activeTab' => $name,
                            ]);
                        }
                    }
                    $this->getOwnerRecord()->touch();

                    return redirect()->to($url);
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

    public function getIconForColumn(string $state): string
    {
        $state = strip_tags($state);
        $state = str_replace(' ', '', $state);
        if ($state && strlen($state) > 0) {
            return 'heroicon-o-check-circle';
        }

        return 'heroicon-o-x-circle';
    }
}
