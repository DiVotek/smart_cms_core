<?php

namespace SmartCms\Core\Models;

use SmartCms\Core\Traits\HasBreadcrumbs;
use SmartCms\Core\Traits\HasRoute;
use SmartCms\Core\Traits\HasSeo;
use SmartCms\Core\Traits\HasSlug;
use SmartCms\Core\Traits\HasSorting;
use SmartCms\Core\Traits\HasStatus;
use SmartCms\Core\Traits\HasTemplate;
use SmartCms\Core\Traits\HasTranslate;
use SmartCms\Core\Traits\HasViews;

/**
 * class Page
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the model.
 * @property string $slug The slug of the model.
 * @property int $sorting The sorting of the model.
 * @property string $image The image of the model.
 * @property bool $status The status of the model.
 * @property int $views The views of the model.
 * @property int $parent_id The parent identifier for the model.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property Page $parent The parent of the model.
 * @property Page[] $children The children of the model.
 * @property bool $is_nav The navigation status of the model.
 */
class Page extends BaseModel
{
    use HasBreadcrumbs;
    use HasRoute;
    use HasSeo;
    use HasSlug;
    use HasSorting;
    use HasStatus;
    use HasTemplate;
    use HasTranslate;
    use HasViews;

    protected $fillable = [
        'name',
        'slug',
        'sorting',
        'image',
        'status',
        'views',
        'parent_id',
        'is_nav',
        'nav_settings',
        'custom',
    ];

    protected $casts = [
        'is_nav' => 'boolean',
        'nav_settings' => 'array',
        'custom' => 'array',
    ];

    public function getBreadcrumbs(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }

    public function route(): string
    {
        $slugs = [];
        $current = $this;

        while ($current) {
            array_unshift($slugs, $current->slug);
            $current = $current->parent;
        }

        return tRoute('page', ['slug' => implode('/', $slugs)]);
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
