<?php

declare(strict_types=1);

namespace App\Actions\LibraryItem;

class GetUserLibraryItem
{
    public function handle($id)
    {
        return $id
            ->with('item')
            ->with('user')
            ->where('id', $id->id)
            ->first();
    }
}
