<?php

namespace SmartCms\Core\Components\Microdata;

use App\Models\Faq;
use App\Service\MultiLang;
use Closure;
use Illuminate\Contracts\View\View;

class FaqPage extends Microdata
{
    public function __construct($template)
    {
        $properties = $this->buildData($template);
        parent::__construct('FAQPage', $properties);
    }

    public function render(): View|Closure|string
    {
        return '<x-microdata :type="$type" :properties="$properties" />';
    }

    public function buildData($entity): array
    {
        $faq = array_filter(array_map(function ($item) {
            return $item['faq'] ?? null;
        }, $entity));
        $values = [];
        $data = [];

        foreach ($values as $value) {
            $data['mainEntity'][] = (object) [
                '@type' => 'Question',
                'name' => $value[current_lang() . '_question'],
                'acceptedAnswer' => (object) [
                    '@type' => 'Answer',
                    'text' => $value[current_lang() . '_answer'],
                ],
            ];
        }

        return $data;
    }
}
