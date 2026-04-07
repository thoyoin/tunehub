<?php

namespace Tests\Feature;

use App\Models\Release;
use App\Models\User;
use App\Services\MinioService;
use getID3;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class CreateReleaseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_artist_can_create_release(): void
    {
        $artist = User::factory()->premium()->create();

        $this->actingAs($artist);

        $minioMock = Mockery::mock(MinioService::class);

        $minioMock->shouldReceive('storeCover')
            ->once()
            ->andReturn('releases/covers/fake-cover.jpg');

        $minioMock->shouldReceive('storeTrack')
            ->twice()
            ->andReturn(
                'releases/tracks/audio1.jpg',
                'releases/tracks/audio2.jpg',
            );

        $this->app->instance(MinioService::class, $minioMock);

        $getId3Mock = Mockery::mock(getID3::class);

        $getId3Mock->shouldReceive('analyze')
            ->twice()
            ->andReturn(
                ['playtime_seconds' => 180],
                ['playtime_seconds' => 200],
            );

        $this->app->instance(getID3::class, $getId3Mock);

        $cover = UploadedFile::fake()->image('cover.jpg');
        $audio1 = UploadedFile::fake()->create('audio1.mp3', 1000, 'audio/mp3');
        $audio2 = UploadedFile::fake()->create('audio2.mp3', 1000, 'audio/mp3');

        $response = $this->postJson('/api/track', [
            'releaseTitle' => 'testTitle',
            'artist' => 'testArtist',
            'type' => 'album',
            'cover_url' => $cover,
            'release_date' => now()->addMonth()->toDateString(),
            'title' => ['Track 1', 'Track 2'],
            'audio_url' => [$audio1, $audio2],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('releases', [
            'title' => 'testTitle',
            'user_id' => $artist->id,
        ]);

        $release = Release::where('title', 'testTitle')->first();

        $this->assertDatabaseHas('tracks', [
            'title' => 'Track 1',
            'release_id' => $release->id,
        ]);
    }

    public function test_non_artist_cannot_create_release(): void
    {
        $artist = User::factory()->create();

        $this->actingAs($artist);

        $cover = UploadedFile::fake()->image('cover.jpg');
        $audio1 = UploadedFile::fake()->create('audio1.mp3', 1000, 'audio/mp3');
        $audio2 = UploadedFile::fake()->create('audio2.mp3', 1000, 'audio/mp3');

        $response = $this->postJson('/api/track', [
            'releaseTitle' => 'testTitle',
            'artist' => 'testArtist',
            'type' => 'album',
            'cover_url' => $cover,
            'release_date' => now()->addMonth()->toDateString(),
            'title' => ['Track 1', 'Track 2'],
            'audio_url' => [$audio1, $audio2],
        ]);

        $response->assertStatus(403);
    }
}
