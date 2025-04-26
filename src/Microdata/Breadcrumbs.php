<?php

namespace SmartCms\Core\Microdata;

use SmartCms\Core\Support\Microdata;

class Breadcrumbs extends Microdata
{
    public static function type(): string
    {
        return 'BreadcrumbList';
    }

    public function build(): array
    {
        $i = 1;
        $data = [];
        foreach ($this->properties as $item) {
            if (is_object($item)) {
                $item = (array) $item;
            }
            $data['itemListElement'][] = (object) [
                '@type' => 'ListItem',
                'position' => $i++,
                'name' => $item['name'] ?? '',
                'item' => $item['link'] ?? '',
            ];
        }
        return $data;
    }
}
