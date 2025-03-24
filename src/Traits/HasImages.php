<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasImages
 */
trait HasImages
{
    public function initializeHasImages()
    {
        $this->mergeCasts([
            $this->getImagesColumn() => 'array',
        ]);
    }

    protected static function bootHasImages(): void
    {
        static::booting(function (Model $model) {
            $model->mergeCasts([$model->getImagesColumn() => 'array']);
        });
    }

    public function getImagesColumn(): string
    {
        return property_exists($this, 'imagesColumn') ? $this->imagesColumn : 'images';
    }

    protected function image(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->images[0] ?? '',
        );
    }
}
