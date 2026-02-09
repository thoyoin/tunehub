<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Release extends Model
{
    protected $fillable = [
        'title',
        'type',
        'artist',
        'cover_url',
        'release_date',
        'status',
    ];

    protected $appends = ['released_in'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Release $release) {
            $release->libraryItem()?->delete();
        });
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
}
