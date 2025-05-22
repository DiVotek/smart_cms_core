<?php

namespace SmartCms\Core\Admin\Support;

use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Extenders\PanelExtender;

class BaseSetup
{
    use AsAction;

    public function __construct(protected PanelExtender $extender) {}
}
