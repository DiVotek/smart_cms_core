<?php

namespace SmartCms\Core\Microdata;

use Carbon\Carbon;
use SmartCms\Core\Support\Microdata;

class NewsArticle extends Microdata
{
    public static function type(): string
    {
        return 'NewsArticle';
    }

    public function build(): array
    {
        $entity = (object)$this->properties;

        if (!$entity) {
            return [];
        }

        return [
            '@type' => 'NewsArticle',
            'url' => url()->current(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => company_name(),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => validateImage(logo()),
                ],
            ],
            'headline' => $entity->heading ?? $entity->name ?? '',
            'articleBody' => $entity->summary ?? '',
            'image' => validateImage($entity->image ?? no_image()),
            'datePublished' => Carbon::parse($entity->created_at)->toIso8601String(),
            'dateModified' => Carbon::parse($entity->updated_at)->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url()->current(),
            ],
        ];
    }
}
