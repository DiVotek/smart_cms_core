<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\BaseModel;
use SmartCms\Core\Traits\HasBreadcrumbs;
use SmartCms\Core\Traits\HasRoute;
use SmartCms\Core\Traits\HasStatus;
use SmartCms\Core\Traits\HasTable;

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
 */
class Page extends BaseModel
{
    use HasBreadcrumbs;
    use HasRoute;
    use HasStatus;
    use HasTable;

    protected $fillable = [
        'name',
        'slug',
        'sorting',
        'image',
        'status',
        'views',
        'parent_id',
    ];

    public static function getDb(): string
    {
        return 'pages';
    }

    protected $table = 'pages';

    public function getBreadcrumbs(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }

    public function route(): string
    {
        return '/';
        // route('page', ['slug' => $this->slug]);
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
