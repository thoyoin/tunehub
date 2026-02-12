<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Auth\SignUp;
use App\Actions\LibraryItem\CreateLibraryItem;
use App\Actions\Playlist\CreateStarterPlaylist;
use Illuminate\Support\Facades\DB;

class SignUpService
{
    public function __construct(
        public SignUp $signUp,
        public CreateStarterPlaylist $createStarterPlaylist,
        public CreateLibraryItem $createLibraryItem,
    ) {}

    public function handle($request): void
    {
        DB::transaction(function () use ($request) {
            $user = $this->signUp->handle($request);

            $playlist = $this->createStarterPlaylist->handle($user);

            $this->createLibraryItem->handle($user, $playlist);
        });
    }
}
