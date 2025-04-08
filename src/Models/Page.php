<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * Class Page
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the page.
 * @property string $slug The slug of the page for URLs.
 * @property int $sorting The sorting order of the page.
 * @property string $image The image path for the page.
 * @property bool $status The status of the page.
 * @property int $parent_id The parent page identifier.
 * @property int $views The number of page views.
 * @property int $layout_id The layout identifier.
 * @property array|null $custom Custom data for the page.
 * @property array|null $layout_settings Layout-specific settings.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \SmartCms\Core\Models\Layout $layout The layout used by this page.
 * @property-read \SmartCms\Core\Models\Page $parent The parent page.
 * @property-read \Illuminate\Database\Eloquent\Collection|\SmartCms\Core\Models\Page[] $children The child pages.
 * @property-read array $breadcrumbs The breadcrumbs for this page.
 */
class Page extends BaseModel
{
    use HasBreadcrumbs;
    use HasFactory;
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

    public function templates()
    {
        return $this->morphMany(Template::class, 'entity');
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
