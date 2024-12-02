<?php

namespace SmartCms\Core\Models;

class Field extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'placeholder' => 'array',
        'options' => 'array',
        'label' => 'array',
        'description' => 'array',
        'mask' => 'array',
    ];
}
