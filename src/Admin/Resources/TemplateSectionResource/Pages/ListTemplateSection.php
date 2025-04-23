<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use SmartCms\Core\Admin\Base\Pages\BaseListRecords;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Services\TableSchema;

class ListTemplateSection extends BaseListRecords
{
    protected static string $resource = TemplateSectionResource::class;

    public bool $isFiltered = false;

    public function getBreadcrumbs(): array
    {
        if (config('shared.admin.breadcrumbs', false)) {
            return parent::getBreadcrumbs();
        }

        return [];
    }

    public function updatedTableFilters(): void
    {
        $this->isFiltered = !blank($this->getTableFilterState('status'));
    }

    public function getTabs(): array
    {
        return [
            Tab::make(_nav('used'))->badge(function (Builder $query) {
                $query = TemplateSection::query()->withoutGlobalScopes();
                $filterValue = $this->getTableFilterState('status')['value'] ?? null;
                if ($this->isFiltered && $filterValue != null) {
                    $q = $query->whereHas('templates');
                    if ($this->getTableFilterState('status')) {
                        $q->where('status', $this->getTableFilterState('status'));
                    }

                    return $q->count();
                }

                return TemplateSection::query()->withoutGlobalScopes()->whereHas('templates')->count();
            })->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('templates');
            }),
            Tab::make(_nav('not_used'))->badge(function (Builder $query) {
                $query = TemplateSection::query()->withoutGlobalScopes();
                $filterValue = $this->getTableFilterState('status')['value'] ?? null;
                if ($this->isFiltered && $filterValue != null) {
                    $q = $query->whereDoesntHave('templates');
                    if ($this->getTableFilterState('status')) {
                        $q->where('status', $this->getTableFilterState('status'));
                    }

                    return $q->count();
                }

                return TemplateSection::query()->withoutGlobalScopes()->whereDoesntHave('templates')->count();
            })->modifyQueryUsing(function (Builder $query) {
                $query->whereDoesntHave('templates');
            }),
        ];
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\Action::make('help')
    //             ->help(_hints('help.page')),
    //         Actions\CreateAction::make()->create(),
    //     ];
    // }
}
