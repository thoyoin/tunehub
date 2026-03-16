<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'started_at',
        'expires_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
