<?php

namespace SmartCms\Core\Models;

use SmartCms\Core\Traits\HasBreadcrumbs;
use SmartCms\Core\Traits\HasLayoutSettings;
use SmartCms\Core\Traits\HasParent;
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
    use HasLayoutSettings;
    use HasParent;
    use HasRoute;
    use HasSeo;
    use HasSlug;
    use HasSorting;
    use HasStatus;
    use HasTemplate;
    use HasTranslate;
    use HasViews;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'custom' => 'array',
        'layout_settings' => 'array',
    ];

    public function layout()
    {
        return $this->belongsTo(Layout::class);
    }

    public function getBreadcrumbs(): array
    {
        return once(function () {
            $breadcrumbs = [
                [
                    'name' => $this->name() ?? '',
                    'link' => $this->route(),
                ],
            ];
            if ($this->parent_id) {
                $parent = $this->getCachedParent();
                if ($parent) {
                    $breadcrumbs = array_merge($parent->getBreadcrumbs(), $breadcrumbs);
                }
            }

            return $breadcrumbs;
        });
    }

    public function route(): string
    {
        return once(function () {
            $slugs = [];
            $current = $this;

            while ($current) {
                array_unshift($slugs, $current->slug);
                $current = $current->getCachedParent();
            }

            return tRoute('cms.page', ['slug' => implode('/', $slugs)]);
        });
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
