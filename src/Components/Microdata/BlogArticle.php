<?php

namespace SmartCms\Core\Components\Microdata;

use Carbon\Carbon;
use Illuminate\Support\Facades\Context;
use SmartCms\Core\Models\Page;

class BlogArticle extends Microdata
{
    public object $entity;

    public function __construct(object $entity)
    {
        $this->entity = $entity;
        parent::__construct('BlogPosting', $this->buildData());
    }

    public function buildData(): array
    {
        $hostPage = Context::get('host', new Page);
        $image = validateImage($this->entity->image ?? no_image());

        return [
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $hostPage->route(),
            ],
            'headline' => $this->entity->heading ?? $this->entity->name ?? '',
            'name' => $this->entity->name ?? '',
            'description' => $this->entity->summary ?? '',
            // "datePublished": "2024-01-05T08:00:00+08:00",
            //   "dateModified": "2024-02-05T09:20:00+08:00"
            'datePublished' => Carbon::parse($this->entity->created_at)->toIso8601String(),
            'dateModified' => Carbon::parse($this->entity->updated_at)->toIso8601String(),
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
                '@id' => $this->entity->parent->link,
                'name' => $this->entity->parent->name ?? '',
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
