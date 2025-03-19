<?php

namespace SmartCms\Core\Components\Pages;

use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;

class StaticPage extends PageComponent
{
    public function __construct(Page $entity)
    {
        // $componentKey = 'static-page-component';
        // $defaultTemplate = _settings('static_page_template', []);
        // $menu_section_parent = $entity->id;
        // $parent = $entity->parent;
        // if ($entity->parent_id) {
        //     if ($parent && $parent->parent_id && $parent->parent) {
        //         $menu_section_parent = $parent->parent->id;
        //     } else {
        //         $menu_section_parent = $parent->id;
        //     }
        // }
        // $section = MenuSection::query()->where('parent_id', $menu_section_parent)->first();
        // if ($section) {
        //     if ($parent) {
        //         if ($parent->parent_id) {
        //             $defaultTemplate = $section->template ?? $defaultTemplate;
        //         } else {
        //             if ($section->is_categories) {
        //                 $defaultTemplate = $section->categories_template ?? $defaultTemplate;
        //             } else {
        //                 $defaultTemplate = $section->template ?? $defaultTemplate;
        //             }
        //         }
        //     }
        // }
        parent::__construct($entity);
    }
}
