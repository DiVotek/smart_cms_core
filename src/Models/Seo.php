<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * class Seo
 *
 * @property int $id The unique identifier for the model.
 * @property string $title The title of the model.
 * @property string $heading The heading of the model.
 * @property string $summary The summary of the model.
 * @property string $content The content of the model.
 * @property string $description The description of the model.
 * @property string $keywords The keywords of the model.
 * @property int $language_id The language identifier for the model.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property Language $language The language of the model.
 * @property mixed $seoable The seoable of the model.
 * @property string $seoable_type The seoable type of the model.
 * @property int $seoable_id The seoable identifier for the model.
 */
class Seo extends BaseModel
{
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
