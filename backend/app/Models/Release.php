<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Release extends Model
{
    protected $fillable = [
        'title',
        'artist',
        'cover_url',
        'release_date',
        'release_type',
        'item_type',
        'status',
        'user_id'
    ];

    protected $appends = [
        'released_in',
        'release_duration'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Release $release) {
            $release->libraryItem()?->delete();
        });
    }

    public function getReleaseDurationAttribute(): string
    {
        $nonformatted = $this->tracks()->sum('duration');

        $minutes = floor($nonformatted / 60);
        $seconds = $nonformatted % 60;

        return sprintf('%2d min %02d sec', $minutes, $seconds);
    }

    public function getReleasedInAttribute(): ?string
    {
        return Carbon::create($this->release_date)->toFormattedDateString();
    }

    public function tracks(): hasMany
    {
        return $this->hasMany(Track::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function libraryItem(): MorphOne
    {
        return $this->morphOne(LibraryItem::class, 'item');
    }

    public function recentlyPlayed(): MorphMany
    {
        return $this->morphMany(RecentlyPlayed::class, 'item');
    }
}
