<?php

namespace SmartCms\Core\Components\Pages;

use SmartCms\Core\Models\Page;

class StaticPage extends PageComponent
{
    public function __construct(Page $entity)
    {
        $componentKey = 'static-page-component';
        $defaultTemplate = setting(config('settings.static_page.template'), []);
        parent::__construct($entity, $componentKey, [], $defaultTemplate);
    }
}
