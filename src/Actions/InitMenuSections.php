<?php

namespace SmartCms\Core\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;

class InitMenuSections
{
    use AsAction;

    public function handle()
    {
        foreach (config('menu_sections', []) as $section) {
            $name = $section['name'];
            $icon = $section['icon'] ?? 'heroicon-c-academic-cap';
            $layout = Layout::query()->where('path', $section['layout'])->first();
            $categoriesEnabled = $section['categories']['enabled'] ?? false;
            $categoriesLayout = Layout::query()->where('path', $section['categories']['layout'] ?? null)->first();
            $itemsLayout = Layout::query()->where('path', $section['items']['layout'] ?? null)->first();
            $customFields = $menuSection['items']['schema'] ?? [];
            $slug = \Illuminate\Support\Str::slug($name);
            $parent_id = null;
            $existedSection = MenuSection::query()->where('name', $name)->first();
            if (! $existedSection || $existedSection->parent_id == null) {
                if (Page::query()->where('slug', $slug)->exists()) {
                    $parent_id = Page::query()->where('slug', $slug)->first()->id;
                } else {
                    $page = Page::query()->create([
                        'name' => $name,
                        'slug' => $slug,
                    ]);
                    $parent_id = $page->id;
                }
            } else {
                $parent_id = $existedSection->parent_id;
            }
            if ($layout) {
                Page::query()->where('id', $parent_id)->update(['layout_id' => $layout->id]);
            }
            $data = [
                'icon' => $icon,
                'parent_id' => $parent_id,
                'is_categories' => $categoriesEnabled,
                'custom_fields' => $customFields,
                'sorting' => MenuSection::query()->max('sorting') + 1,
                'categories_layout_id' => $categoriesLayout->id ?? null,
                'items_layout_id' => $itemsLayout->id ?? null,
            ];
            MenuSection::query()->updateOrCreate(['name' => $name], $data);
        }
    }
}
