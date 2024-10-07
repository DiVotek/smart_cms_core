<?php

namespace SmartCms\Core\Components\Pages;

use SmartCms\Core\Models\Page;

class StaticPage extends PageComponent
{
    public function __construct(Page $entity)
    {
        $componentKey = 'static-page-component';
        $defaultTemplate = _settings('static_page_template', []);
        parent::__construct($entity, $componentKey, [], $defaultTemplate);
    }
}
