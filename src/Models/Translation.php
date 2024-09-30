<?php

namespace SmartCms\Core\Models;

/**
 * class Translation
 *
 * @property int $id The unique identifier for the model.
 * @property string $key The key of the model.
 * @property int $language_id The language identifier for the model.
 * @property string $value The value of the model.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property Language $language The language of the model.
 */
class Translation extends BaseModel
{
    protected $fillable = [
        'key',
        'language_id',
        'value',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
