<?php

namespace SmartCms\Core\Models;

use SmartCms\Core\Traits\HasTranslate;

class Field extends BaseModel
{
    use HasTranslate;

    protected $guarded = [];

    protected $casts = [
        'placeholder' => 'array',
        'options' => 'array',
        'label' => 'array',
        'description' => 'array',
        'mask' => 'array',
        'data' => 'array',
    ];
}
