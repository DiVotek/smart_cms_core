<?php

namespace SmartCms\Core\Components\Microdata;

use Closure;
use Illuminate\Contracts\View\View;

class Breadcrumbs extends Microdata
{
    public function __construct($data)
    {
        $properties = $this->buildData($data);
        parent::__construct('BreadcrumbList', $properties);
    }

    public function render(): View|Closure|string
    {
        return '<x-s::microdata.microdata :type="$type" :properties="$properties" />';
    }

    public function buildData($entity): array
    {
        $i = 1;
        foreach ($entity as $item) {
            $data['itemListElement'][] = (object) [
                '@type' => 'ListItem',
                'position' => $i++,
                'name' => $item['name'],
                'item' => $item['link'],
            ];
        }

        return $data;
    }
}
