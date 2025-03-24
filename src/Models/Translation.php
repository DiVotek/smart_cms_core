<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Translation
 *
 * @property int $id The unique identifier for the model.
 * @property string $key The translation key.
 * @property int $language_id The language identifier.
 * @property string $value The translated value.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \SmartCms\Core\Models\Language $language The language of this translation.
 */
class Translation extends BaseModel
{
    use HasFactory;
    protected $guarded = [];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
