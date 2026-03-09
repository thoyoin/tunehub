<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use ClickHouseDB\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Track extends Model
{
    protected $fillable = [
        'title',
        'release_id',
        'user_id',
        'artist',
        'duration',
        'cover_url',
        'audio_url',
        'release_date',
        'position',
    ];

    protected $appends = [
        'formatted_duration',
        'added_ago',
        'released_in',
        'playlist_ids',
        'plays'
    ];

    public function getPlaylistIdsAttribute(): array
    {
        if (! $this->relationLoaded('playlists')) {
            return [];
        }

        return $this->playlists()
            ->pluck('playlists.id')
            ->toArray();
    }

    public function getPlaysAttribute(): int
    {
        $client = new Client([
            'host' => env('CLICKHOUSE_HOST'),
            'port' => env('CLICKHOUSE_PORT', 8123),
            'username' => env('CLICKHOUSE_USER'),
            'password' => env('CLICKHOUSE_PASSWORD'),
        ]);

        $client->database('default');

        $result = $client->select(
            "SELECT count() AS plays FROM track_plays WHERE track_id = {$this->id}"
        );

        return (int) $result->rows()[0]['plays'];
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getAddedAgoAttribute(): ?string
    {
        if (! $this->pivot?->created_at) {
            return null;
        }

        return Carbon::parse($this->pivot->created_at)->diffForHumans();
    }

    public function getReleasedInAttribute(): ?string
    {
        return Carbon::create($this->release_date)->toFormattedDateString();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class)->withTimestamps();
    }
}
