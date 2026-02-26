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

        return Release::query()
            ->with(['user', 'tracks'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->paginate(10);
    }
}
