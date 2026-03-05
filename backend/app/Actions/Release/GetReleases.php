<?php

declare(strict_types=1);

namespace App\Actions\Release;

use App\Models\Release;
use Illuminate\Pagination\LengthAwarePaginator;

class GetReleases
{
    public function handle($request): LengthAwarePaginator
    {
        $status = $request->query('status');
        $query = $request->query('query');

        return Release::query()
            ->with(['user', 'tracks'])
            ->when($query, fn ($q) => $q
                ->where('title', 'like', "%$query%"))
                ->orWhereHas('user', fn ($q) => $q
                    ->where('username', 'like', "%$query%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->paginate(10);
    }
}
