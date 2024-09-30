<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * class Translate
 *
 * @property int $id The unique identifier for the model.
 * @property string $value The value of the model.
 * @property int $language_id The language identifier for the model.
 * @property int $entity_id The entity
 * @property string $entity_type The entity type of the model.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property Language $language The language of the model.
 */
class Translate extends BaseModel
{
    protected $guarded = [];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
