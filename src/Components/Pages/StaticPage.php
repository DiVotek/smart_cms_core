<?php

namespace SmartCms\Core\Components\Pages;

use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Models\Page;

class StaticPage extends PageComponent
{
    public function __construct(Page $entity)
    {
        parent::__construct($entity);
    }
}
