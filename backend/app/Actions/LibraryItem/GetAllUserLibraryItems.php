<?php

declare(strict_types=1);

namespace App\Actions\LibraryItem;

use Illuminate\Support\Facades\Auth;

class GetAllUserLibraryItems
{
    public function handle()
    {
        $user = Auth::user();

        return $user
            ->libraryItems()
            ->with('user')
            ->with('item.tracks')
            ->get();
    }
}
