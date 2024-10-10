<?php

namespace SmartCms\Core\Models;

use Illuminate\Notifications\Notifiable;
use SmartCms\Core\Traits\HasTable;

class Menu extends BaseModel
{
    use HasTable;
    use Notifiable;

    protected $guarded = [];

    protected $casts = [
        'value' => 'array',
    ];
}
