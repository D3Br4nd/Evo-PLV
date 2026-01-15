<?php

namespace Tests\Feature\Admin;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCheckinSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_members_by_name()
    {
        $admin = User::factory()->admin()->create();
        $member1 = User::factory()->create([
            'name' => 'Mario Rossi',
            'email' => 'mario@example.com',
        ]);
        $member2 = User::factory()->create([
            'name' => 'Luigi Verdi',
            'email' => 'luigi@example.com',
        ]);

        $this->actingAs($admin)
            ->getJson(route('members.search', ['q' => 'Mario']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Mario Rossi']);

        $this->actingAs($admin)
            ->getJson(route('members.search', ['q' => 'Verdi']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Luigi Verdi']);
    }

    public function test_search_returns_membership_status()
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['name' => 'Test User']);
        
        // Active membership for this year
        Membership::create([
            'user_id' => $user->id,
            'year' => now()->year,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->getJson(route('members.search', ['q' => 'Test']))
            ->assertOk()
            ->assertJsonFragment(['status' => 'active']);
    }

    public function test_search_requires_minimum_characters()
    {
        $admin = User::factory()->admin()->create();
        
        $this->actingAs($admin)
            ->getJson(route('members.search', ['q' => 'a']))
            ->assertOk()
            ->assertJsonCount(0); // Should return empty array
    }
}
