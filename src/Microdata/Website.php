<?php

namespace SmartCms\Core\Microdata;

use Illuminate\Support\Facades\Context;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Support\Microdata;

class Website extends Microdata
{
    public static function type(): string
    {
        return 'WebSite';
    }

    public function build(): array
    {
        $hostPage = Context::get('host', new Page);

        return [
            '@type' => 'WebSite',
            'url' => $hostPage->route(),
        ];
    }
}
