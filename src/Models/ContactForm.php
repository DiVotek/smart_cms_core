<?php

namespace SmartCms\Core\Models;

use SmartCms\Core\BaseModel;

/**
 * class ContactForm
 *
 * @property int $id The unique identifier for the model.
 * @property int $form_id The form identifier for the model.
 * @property string $name The name of the model.
 */
class ContactForm extends BaseModel
{
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
