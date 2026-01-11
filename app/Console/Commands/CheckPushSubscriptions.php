<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckPushSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:check {user_id? : UUID of the user to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check push subscription status for a user or all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            $this->checkUser($userId);
        } else {
            $this->checkAll();
        }

        return 0;
    }

    private function checkUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            $this->error("User not found: {$userId}");
            return;
        }

        $user->load('pushSubscriptions');

        $this->info("User: {$user->name} ({$user->email})");
        $this->info("UUID: {$user->id}");
        $this->line('');

        if ($user->pushSubscriptions->isEmpty()) {
            $this->warn('No push subscriptions registered');
        } else {
            $this->info("Push Subscriptions: {$user->pushSubscriptions->count()}");
            $this->line('');

            foreach ($user->pushSubscriptions as $index => $sub) {
                $this->line("Subscription #" . ($index + 1));
                $this->line("  ID: {$sub->id}");
                $this->line("  Endpoint: " . substr($sub->endpoint, 0, 60) . '...');
                
                // Detect browser
                $browser = 'Unknown';
                if (str_contains($sub->endpoint, 'fcm.googleapis.com')) {
                    $browser = 'Chrome/Android';
                } elseif (str_contains($sub->endpoint, 'updates.push.services.mozilla.com')) {
                    $browser = 'Firefox';
                } elseif (str_contains($sub->endpoint, 'web.push.apple.com')) {
                    $browser = 'Safari';
                }
                $this->line("  Browser: {$browser}");
                
                $this->line("  Created: {$sub->created_at->format('Y-m-d H:i:s')}");
                $this->line("  Updated: {$sub->updated_at->format('Y-m-d H:i:s')}");
                $this->line('');
            }
        }
    }

    private function checkAll()
    {
        $users = User::with('pushSubscriptions')
            ->whereHas('pushSubscriptions')
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No users with push subscriptions found');
            return;
        }

        $this->info("Users with push subscriptions: {$users->count()}");
        $this->line('');

        $table = [];
        foreach ($users as $user) {
            $table[] = [
                substr($user->id, 0, 8) . '...',
                $user->name,
                $user->email,
                $user->pushSubscriptions->count(),
                $user->pushSubscriptions->first()->created_at->format('Y-m-d H:i'),
            ];
        }

        $this->table(
            ['UUID', 'Name', 'Email', 'Subscriptions', 'Last Registered'],
            $table
        );

        $this->line('');
        $this->info('Total push subscriptions: ' . \DB::table('push_subscriptions')->count());
    }
}
