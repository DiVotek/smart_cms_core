<?php

namespace SmartCms\Core\Components\Microdata;

use Closure;
use Illuminate\Contracts\View\View;

class Organization extends Microdata
{
    public function __construct()
    {
        $properties = $this->buildData();
        parent::__construct('Organization', $properties);
    }

    public function render(): View|Closure|string
    {
        return '<x-microdata :type="$type" :properties="$properties" />';
    }

    public function buildData(): array
    {
        return [
            'name' => setting(config('settings.company_name'), 'Company name'),
            'url' => url('/'),
            'logo' => asset(logo()),
            'contactPoint' => [],
        ];
    }
}
