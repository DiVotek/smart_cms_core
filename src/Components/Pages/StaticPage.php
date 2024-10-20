<?php

namespace SmartCms\Core\Components\Pages;

use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;

class StaticPage extends PageComponent
{
    public function __construct(Page $entity)
    {
        $componentKey = 'static-page-component';
        $defaultTemplate = _settings('static_page_template', []);
        if ($entity->parent_id) {
            $section = MenuSection::query()->where('parent_id', $entity->parent_id)->first();
            if ($section && $section->template && !empty($section->template)) {
                $defaultTemplate = $section->template;
            }
        }
        parent::__construct($entity, $componentKey, [], $defaultTemplate);
    }
}
