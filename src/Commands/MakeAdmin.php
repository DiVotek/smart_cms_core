<?php

namespace SmartCms\Core\Commands;

use Illuminate\Console\Command;
use SmartCms\Core\Models\Admin;

class MakeAdmin extends Command
{
    protected $signature = 'make:admin';

    protected $description = 'Make scms admin';

    public function handle()
    {
        $name = $this->ask('Enter admin username');
        if (Admin::query()->where('username', $name)->exists()) {
            $this->error('Admin already exists');
            return;
        }
        $email = $this->ask('Enter admin email');
        if (Admin::query()->where('email', $email)->exists()) {
            $this->error('Admin already exists');
            return;
        }
        $password = $this->secret('Enter admin password');
        if ($this->confirm('Do you wish to continue?', true)) {
            Admin::query()->create([
                'username' => $name,
                'email' => $email,
                'password' => $password,
            ]);
            $this->info('Admin created successfully');
        } else {
            $this->info('Admin creation cancelled');
        }
    }
}
