<?php

declare(strict_types=1);

namespace App\Actions\LibraryItem;

class IsItemRelease
{
    public function handle($libraryItem): bool
    {
        return $libraryItem->item_type == 'release';
    }
}
