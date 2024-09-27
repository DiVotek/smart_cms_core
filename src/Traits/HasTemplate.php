<?php

namespace SmartCms\Core\Traits;

use App\Models\Template;
use Illuminate\Database\Eloquent\Model;

trait HasTemplate
{
    public function initializeHasTemplate()
    {
        $this->mergeFillable(['template']);
        $this->mergeCasts(['template' => 'array']);
    }

    protected static function bootHasTemplate(): void
    {
        static::booting(function (Model $model) {
            $model->mergeFillable(['template']);
            $model->mergeCasts(['template' => 'array']);
        });
    }

    public function template()
    {
        return $this->morphOne(Template::class, 'entity');
    }
}
