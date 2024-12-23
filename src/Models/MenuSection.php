<?php

namespace SmartCms\Core\Models;

use Illuminate\Notifications\Notifiable;
use SmartCms\Core\Traits\HasSorting;
use SmartCms\Core\Traits\HasTable;

class MenuSection extends BaseModel
{
    use HasSorting;
    use HasTable;
    use Notifiable;

    protected $fillable = [
        'sorting',
        'name',
        'is_categories',
        'custom_fields',
        'template',
        'parent_id',
        'icon',
        'categories_template',
    ];

    protected $guarded = [];

    protected $casts = [
        'sorting' => 'integer',
        'name' => 'string',
        'is_categories' => 'boolean',
        'custom_fields' => 'array',
        'template' => 'array',
        'categories_template' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }
}
