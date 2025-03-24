<?php

namespace SmartCms\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Models\Template;

/**
 * Trait HasTemplate
 *
 * @package SmartCms\Core\Traits
 */
trait HasTemplate
{
    public function initializeHasTemplate()
    {
        $this->mergeCasts(['template' => 'array']);
    }

    protected static function bootHasTemplate(): void
    {
        static::booting(function (Model $model) {
            $model->mergeCasts(['template' => 'array']);
        });
    }

    /**
     * Get the template relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function template()
    {
        return $this->morphOne(Template::class, 'entity');
    }
}
