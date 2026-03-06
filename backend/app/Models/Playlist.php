<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Playlist extends Model
{
    protected $fillable = [
        'slug',
        'user_id',
        'title',
        'description',
        'cover_url',
    ];

    protected $appends = [
        'playlist_duration',
        'creation_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Playlist $playlist) {
            $playlist->slug = Str::slug($playlist->title);
        });

        static::deleting(function (Playlist $playlist) {
            $playlist->libraryItem()->delete();
        });
    }

    public function getPlaylistDurationAttribute(): string
    {
        $nonformatted = $this->tracks()->sum('duration');

        $minutes = floor($nonformatted / 60);
        $seconds = $nonformatted % 60;

        return sprintf('%2d min %02d sec', $minutes, $seconds);
    }

    public function getCreationDateAttribute(): string
    {
        return Carbon::create($this->created_at)->toFormattedDateString();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function libraryItem(): MorphOne
    {
        return $this->morphOne(LibraryItem::class, 'item');
    }

    public function tracks(): BelongsToMany
    {
        return $this->belongsToMany(Track::class)
            ->withPivot('position')
            ->orderBy('pivot_position')
            ->withTimestamps();
    }

    public function recentlyPlayed(): MorphMany
    {
        return $this->morphMany(RecentlyPlayed::class, 'item');
    }
}
