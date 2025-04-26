<?php

namespace SmartCms\Core\Microdata;

use SmartCms\Core\Support\Microdata;

class FaqPage extends Microdata
{
    public static function type(): string
    {
        return 'FAQPage';
    }

    public function build(): array
    {
        $entity = $this->properties['entity'] ?? null;

        if (! $entity) {
            return [];
        }

        $faq = array_filter(array_map(function ($item) {
            return $item['faq'] ?? null;
        }, $entity));
        $values = [];
        $data = [];

        foreach ($values as $value) {
            $data['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $value[current_lang().'_question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $value[current_lang().'_answer'],
                ],
            ];
        }

        return [
            '@type' => 'FAQPage',
            ...$data,
        ];
    }
}
