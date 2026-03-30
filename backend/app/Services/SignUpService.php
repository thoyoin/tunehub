<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Auth\SignUp;
use App\Actions\LibraryItem\CreateLibraryItem;
use App\Actions\Playlist\CreateStarterPlaylist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SignUpService
{
    public function __construct(
        public SignUp $signUp,
        public CreateStarterPlaylist $createStarterPlaylist,
        public CreateLibraryItem $createLibraryItem,
    ) {}

    public function handle($request): void
    {
        $request->validated();

        $request->safe()->except('middle_name');

        DB::transaction(function () use ($request) {
            $user = $this->signUp->handle($request);

            $playlist = $this->createStarterPlaylist->handle($user);

            $this->createLibraryItem->handle($user->id, $playlist->id, 'playlist');

            Log::info('New User was signed Up', [
                'username' => $request['username'],
            ]);

            Auth::login($user);
        });
    }
}
