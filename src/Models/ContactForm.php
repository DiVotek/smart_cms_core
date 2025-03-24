<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ContactForm
 *
 * @property int $id The unique identifier for the model.
 * @property int $form_id The form identifier for the model.
 * @property array $data The form submission data.
 * @property string|null $comment Admin comment on the form submission.
 * @property int $status The status of the form submission (0=new, 1=viewed, 2=closed).
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \SmartCms\Core\Models\Form $form The related form.
 */
class ContactForm extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['data' => 'array'];

    public const STATUS_NEW = 0;

    public const STATUS_VIEWED = 1;

    public const STATUS_CLOSED = 2;

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => __('New'),
            self::STATUS_VIEWED => __('Viewed'),
            self::STATUS_CLOSED => __('Closed'),
        ];
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
