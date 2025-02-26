<?php

namespace SmartCms\Core\Components\Microdata;

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
            'datePublished' => $this->entity->created_at,
            'dateModified' => $this->entity->updated_at,
            'publisher' => [
                '@type' => 'Organization',
                'name' => company_name(),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => logo(),
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
                        'url' => logo(),
                    ],
                ],
            ],
        ];
    }
}
