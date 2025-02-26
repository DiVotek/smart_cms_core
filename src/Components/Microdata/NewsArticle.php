<?php

namespace SmartCms\Core\Components\Microdata;

class NewsArticle extends Microdata
{
    public object $entity;

    public function __construct(object $entity)
    {
        $this->entity = $entity;
        parent::__construct('NewsArticle', $this->buildData());
    }

    public function buildData(): array
    {
        return [
            'url' => url()->current(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => company_name(),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => logo(),
                ],
            ],
            'headline' => $this->entity->heading ?? $this->entity->name ?? '',
            'articleBody' => $this->entity->summary ?? '',
            'image' => validateImage($this->entity->image ?? no_image()),
            'datePublished' => $this->entity->created_at,
            'dateModified' => $this->entity->updated_at,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url()->current(),
            ],
        ];
    }
}
