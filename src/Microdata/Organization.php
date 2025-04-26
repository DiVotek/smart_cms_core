<?php

namespace SmartCms\Core\Microdata;

use SmartCms\Core\Support\Microdata;

class Organization extends Microdata
{
    public static function type(): string
    {
        return 'Organization';
    }

    public function build(): array
    {
        return [
            '@type' => 'Organization',
            'name' => _settings('company_name', 'Company name'),
            'url' => url('/'),
            'logo' => validateImage(logo()),
            'contactPoint' => [],
        ];
    }
}
