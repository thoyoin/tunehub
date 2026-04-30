<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        public MinioService $minioService,
    ) {}

    public function update($request): ?Authenticatable
    {
        $data = $request->validate([
            'username' => [
                'sometimes',
                'string',
                'min:3',
                'max:50',
                Rule::unique('users', 'username')->ignore(auth()->id()),
            ],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(auth()->id()),
            ],
            'profile_picture' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_picture')) {
            $url = $this->minioService->storeProfile($data['profile_picture']);
            $data['profile_picture'] = $url;
        }

        $user->update($data);

        Log::info('User was updated', [
            'username' => $data['username'],
        ]);

        return $user;
    }

    public function destroyFromAdmin(User $user): void
    {
        if (auth()->id() === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot delete yourself.',
            ]);
        }

        $user->delete();
    }
}
