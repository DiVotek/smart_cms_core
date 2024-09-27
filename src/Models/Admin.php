<?php

namespace SmartCms\Core\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use SmartCms\Core\Traits\HasTable;

class Admin extends User implements FilamentUser
{
    use HasTable;
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public static function getDb(): string
    {
        return 'admins';
    }

    protected $table = 'admins';

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getNameAttribute(): string
    {
        return $this->username ?? 'Admin';
    }
}
