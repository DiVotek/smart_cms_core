<?php

namespace SmartCms\Core\Components\Microdata;

use Carbon\Carbon;

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
                    'url' => validateImage(logo()),
                ],
            ],
            'headline' => $this->entity->heading ?? $this->entity->name ?? '',
            'articleBody' => $this->entity->summary ?? '',
            'image' => validateImage($this->entity->image ?? no_image()),
            'datePublished' => Carbon::parse($this->entity->created_at)->toIso8601String(),
            'dateModified' => Carbon::parse($this->entity->updated_at)->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url()->current(),
            ],
        ];
    }
}
