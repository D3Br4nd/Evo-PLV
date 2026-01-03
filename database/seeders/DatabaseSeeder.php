<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Membership;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Enums\UserRole;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@prolocoventicanese.it'],
            [
                'name' => 'Massimiliano Admin',
                'password' => 'password', // Will be hashed by model cast
                'role' => UserRole::SuperAdmin->value,
                'membership_status' => 'active',
            ]
        );

        // Member User
        $member = User::updateOrCreate(
            ['email' => 'mario.rossi@example.com'],
            [
                'name' => 'Mario Rossi',
                'password' => 'password',
                'role' => UserRole::Member->value,
                'membership_status' => 'active',
            ]
        );
        
        // Memberships
        $membership = Membership::firstOrCreate(
            [
                'user_id' => $member->id,
                'year' => 2025,
            ],
            [
                'status' => 'active',
                // IMPORTANT: do not rotate qr_token on reseed (it would invalidate issued QR codes)
                'qr_token' => Str::random(32),
            ]
        );

        // Keep status up to date on reseed, without touching qr_token
        if (! $membership->wasRecentlyCreated && $membership->status !== 'active') {
            $membership->update(['status' => 'active']);
        }

        // Events
        Event::firstOrCreate(
            [
                'title' => '46ª Fiera Campionaria Venticano',
                'start_date' => '2025-04-24 09:00:00',
            ],
            [
                'end_date' => '2025-04-27 22:00:00',
                'type' => 'fair',
                'metadata' => ['location' => 'Quartiere Fieristico', 'organizer' => 'Pro Loco Venticanese'],
            ]
        );

        Event::firstOrCreate(
            [
                'title' => 'Sagra del Prosciutto',
                'start_date' => '2025-08-30 18:00:00',
            ],
            [
                'end_date' => '2025-08-31 23:59:00',
                'type' => 'festival',
                'metadata' => ['location' => 'Piazza Monumento', 'theme' => 'Gastronomy'],
            ]
        );
    }
}