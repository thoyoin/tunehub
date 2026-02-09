<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
            ->withTimestamps();
    }
}
