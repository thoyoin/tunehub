<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\User;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class GetAllUsers
{
    public function handle($request): LengthAwarePaginator
    {
        $search = $request->query('search');

        return User::with('playlists')
            ->with('tracks')
            ->when($search, fn ($query) =>
                $query->where('username', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
            )
            ->paginate(10);
    }
}
