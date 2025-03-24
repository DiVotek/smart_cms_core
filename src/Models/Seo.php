<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Seo
 *
 * @property int $id The unique identifier for the model.
 * @property string $title The SEO title.
 * @property string $heading The page heading.
 * @property string $summary The summary or excerpt.
 * @property string $content The main content.
 * @property string $description The meta description.
 * @property string $keywords The meta keywords.
 * @property int $language_id The language identifier.
 * @property string $seoable_type The type of model this SEO belongs to.
 * @property int $seoable_id The ID of the model this SEO belongs to.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \SmartCms\Core\Models\Language $language The language of this SEO.
 * @property-read mixed $seoable The model this SEO belongs to.
 */
class Seo extends BaseModel
{
    use HasFactory;
    use HasTimestamps;

    protected $guarded = [];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
