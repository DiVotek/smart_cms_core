<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use SmartCms\Core\Traits\HasTable;

/**
 * Class Menu
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the menu.
 * @property array $value The menu items configuration.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 */
class Menu extends BaseModel
{
    use HasFactory;
    use HasTable;
    use Notifiable;

    protected $guarded = [];

    protected $casts = [
        'value' => 'array',
    ];
}
