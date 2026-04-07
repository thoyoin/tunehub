<?php

namespace Tests\Feature;

use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReleasePublicationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_artist_can_publish_approved_release(): void
    {
        $artist = User::factory()->premium()->create();

        $release = Release::factory()->create([
            'user_id' => $artist->id,
            'status' => 'approved',
        ]);

        $this->actingAs($artist);

        $response = $this->patchJson("/api/release/{$release->id}/publish");

        $response->assertStatus(200);

        $this->assertDatabaseHas('releases', [
            'title' => $release->title,
            'status' => 'published',
        ]);
    }

    public function test_artist_cannot_publish_non_approved_release(): void
    {
        $artist = User::factory()->premium()->create();

        $release = Release::factory()->create([
            'user_id' => $artist->id,
            'status' => 'pending',
        ]);

        $this->actingAs($artist);

        $response = $this->patchJson("/api/release/{$release->id}/publish");

        $response->assertStatus(403);

        $this->assertDatabaseMissing('releases', [
            'title' => $release->title,
            'status' => 'published',
        ]);
    }

    public function test_artist_cannot_publish_rejected_release(): void
    {
        $artist = User::factory()->premium()->create();

        $release = Release::factory()->create([
            'user_id' => $artist->id,
            'status' => 'rejected',
        ]);

        $this->actingAs($artist);

        $response = $this->patchJson("/api/release/{$release->id}/publish");

        $response->assertStatus(403);

        $this->assertDatabaseMissing('releases', [
            'title' => $release->title,
            'status' => 'published',
        ]);
    }
}
