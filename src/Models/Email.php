<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'users' => 'array',
    ];
}
