<?php

namespace SmartCms\Core\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use SmartCms\Core\Traits\HasTable;

/**
 * Class Admin
 *
 * @property int $id The unique identifier for the model.
 * @property string $username The username of the admin.
 * @property string $email The email address of the admin.
 * @property string $password The hashed password of the admin.
 * @property string|null $remember_token The token used for "remember me" functionality.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 */
class Admin extends User implements FilamentUser
{
    use HasFactory;
    use HasTable;
    use Notifiable;

    // protected $fillable = [
    //     'username',
    //     'email',
    //     'password',
    //     'telegram_id',
    //     'notifications',
    // ];

    protected $hidden = ['password', 'remember_token'];

    protected $guarded = [];

    protected $casts = [
        'password' => 'hashed',
        'notifications' => 'array',
    ];

    protected $table = 'smart_cms_admins';

    public static function getDb(): string
    {
        return 'admins';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getNameAttribute(): string
    {
        return $this->username ?? 'Admin';
    }
}
