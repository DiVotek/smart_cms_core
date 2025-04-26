<?php

namespace SmartCms\Core\Microdata;

use Carbon\Carbon;
use Illuminate\Support\Facades\Context;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Support\Microdata;

class BlogArticle extends Microdata
{
    public static function type(): string
    {
        return 'BlogPosting';
    }

    public function build(): array
    {
        $entity = (object)$this->properties;

        if (!$entity) {
            return [];
        }

        $hostPage = Context::get('host', new Page);
        $image = validateImage($entity->image ?? no_image());

        return [
            '@type' => 'BlogPosting',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $hostPage->route(),
            ],
            'headline' => $entity->heading ?? $entity->name ?? '',
            'name' => $entity->name ?? '',
            'description' => $entity->summary ?? '',
            'datePublished' => Carbon::parse($entity->created_at)->toIso8601String(),
            'dateModified' => Carbon::parse($entity->updated_at)->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => company_name(),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => validateImage(logo()),
                ],
            ],
            'image' => [
                '@type' => 'ImageObject',
                '@id' => $image,
                'url' => $image,
            ],
            'isPartOf' => [
                '@type' => 'Blog',
                '@id' => $entity->parent->link,
                'name' => $entity->parent->name ?? '',
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => company_name(),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => validateImage(logo()),
                    ],
                ],
            ],
        ];
    }
}
