<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateMerchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_non_artist_cannot_create_merch(): void
    {
        $artist = User::factory()->create();

        $this->actingAs($artist);

        $response = $this->postJson('/api/artists/merch/drop', [
            'item_title' => 'test_title',
            'item_description' => 'test_description',
            'merch_variants' => json_encode([
                [
                    'variant_name' => 'M',
                    'price' => 100,
                    'stock' => 5,
                ],
            ]),
        ]);

        $response->assertStatus(403);
    }

    public function test_artist_can_create_merch(): void
    {
        Role::query()->create([
            'slug' => 'premium',
            'name' => 'Premium',
        ]);

        $artist = User::factory()->premium()->create();

        $this->actingAs($artist);

        $response = $this->postJson('/api/artists/merch/drop', [
            'item_title' => 'test_title',
            'item_description' => 'test_description',
            'merch_variants' => json_encode([
                [
                    'variant_name' => 'M',
                    'price' => 100,
                    'stock' => 5,
                ],
            ]),
        ]);

        $response->assertStatus(201);
    }
}
