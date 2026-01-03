<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('plv:bootstrap-superadmin {--force : Overwrite password even if user exists}', function () {
    $email = env('SUPERADMIN_EMAIL');
    $password = env('SUPERADMIN_PASSWORD');
    $name = env('SUPERADMIN_NAME', 'Super Admin');

    if (! $email) {
        $this->error('Missing SUPERADMIN_EMAIL in .env');
        return 1;
    }

    if (! $password) {
        $this->error('Missing SUPERADMIN_PASSWORD in .env');
        return 1;
    }

    $user = User::query()->where('email', $email)->first();

    if (! $user) {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password, // hashed by cast
            'role' => UserRole::SuperAdmin->value,
            'membership_status' => 'active',
        ]);

        $this->info("Created SuperAdmin: {$user->email}");
        return 0;
    }

    $updates = [
        'name' => $name,
        'role' => UserRole::SuperAdmin->value,
        'membership_status' => 'active',
    ];

    if ($this->option('force')) {
        $updates['password'] = $password; // hashed by cast
    }

    $user->update($updates);

    $this->info("Updated SuperAdmin: {$user->email}".($this->option('force') ? ' (password overwritten)' : ''));

    return 0;
})->purpose('Create or update the SuperAdmin user from SUPERADMIN_* env vars');


