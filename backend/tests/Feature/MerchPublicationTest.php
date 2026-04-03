<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchPublicationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_artist_can_publicate_merch(): void
    {
        $artist = User::factory()->premium()->create();

        $merch = Product::factory()->create([
            'user_id' => $artist->id,
            'status' => 'approved',
        ]);

        $this->actingAs($artist);

        $response = $this->patchJson("/api/artists/merch/{$merch->id}/publish");

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'id' => $merch->id,
            'status' => 'active',
        ]);
    }

    public function test_artist_cannot_publish_non_approved_merch(): void
    {
        $artist = User::factory()->premium()->create();

        $merch = Product::factory()->create([
            'user_id' => $artist->id,
            'status' => 'moderating',
        ]);

        $this->actingAs($artist);

        $response = $this->patchJson("/api/artists/merch/{$merch->id}/publish");

        $response->assertStatus(403);

        $this->assertDatabaseMissing('products', [
            'id' => $merch->id,
            'status' => 'active',
        ]);
    }

    public function test_artist_cannot_publish_alien_merch(): void
    {
        $artist1 = User::factory()->premium()->create();

        $artist2 = User::factory()->premium()->create();

        $merch = Product::factory()->create([
            'user_id' => $artist2->id,
            'status' => 'approved',
        ]);

        $this->actingAs($artist1);

        $response = $this->patchJson("/api/artists/merch/{$merch->id}/publish");

        $response->assertStatus(403);
    }
}
