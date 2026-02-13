<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        public MinioService $minioService,
    ) {}

    public function update($request)
    {
        $data = $request->only(['username', 'email', 'profile_picture']);
        $user = Auth::user();

        if ($request->hasFile('profile_picture')) {
            $url = $this->minioService->storeProfile($request->file('profile_picture'));
            $data['profile_picture'] = $url;
        }

        $user->update($data);

        return $user;
    }
}
