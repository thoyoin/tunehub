<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
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

    protected $appends = ['formatted_duration', 'added_ago', 'released_in'];

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
