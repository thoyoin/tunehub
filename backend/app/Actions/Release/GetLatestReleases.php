<?php

declare(strict_types=1);

namespace App\Actions\Release;

use App\Models\Release;
use Illuminate\Database\Eloquent\Collection;

class GetLatestReleases
{
    public function handle(): Collection
    {
         return Release::with('user')
            ->orderBy('release_date', 'desc')
            ->limit(5)
            ->get();
    }
}
