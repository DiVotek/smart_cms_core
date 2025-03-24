<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use SmartCms\Core\Traits\HasSorting;
use SmartCms\Core\Traits\HasTable;

/**
 * Class MenuSection
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the menu section.
 * @property int $parent_id The parent page identifier.
 * @property int $sorting The sorting order of the menu section.
 * @property bool $is_categories Whether this is a categories section.
 * @property array $custom_fields Custom fields for the menu section.
 * @property array $template Template configuration for the menu section.
 * @property array $categories_template Template configuration for categories.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \SmartCms\Core\Models\Page $page The parent page.
 */
class MenuSection extends BaseModel
{
    use HasFactory;
    use HasSorting;
    use HasTable;
    use Notifiable;

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
