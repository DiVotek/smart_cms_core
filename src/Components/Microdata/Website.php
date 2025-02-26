<?php

namespace SmartCms\Core\Components\Microdata;

use Illuminate\Support\Facades\Context;
use SmartCms\Core\Models\Page;

class Website extends Microdata
{
    public function __construct()
    {
        parent::__construct('WebSite', $this->buildData());
    }

    public function buildData(): array
    {
        $hostPage = Context::get('host', new Page);

        return [
            'url' => $hostPage->route(),
        ];
    }
}
