<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserService
{
    public function __construct(
        public MinioService $minioService,
    ) {}

    /**
     * @throws AuthenticationException
     */
    public function update($request): User
    {
        $user = $request->user();

        if (!$user instanceof User) {
            throw new AuthenticationException();
        }

        $data = $request->validate([
            'username' => [
                'sometimes',
                'string',
                'min:3',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'profile_picture' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('profile_picture')) {
            $url = $this->minioService->storeProfile($data['profile_picture']);
            $data['profile_picture'] = $url;
        }

        $user->update($data);

        $user->refresh();

        Log::info('User was updated', [
            'username' => $user->username,
        ]);

        return $user;
    }

    public function destroyFromAdmin(User $user): void
    {
        DB::transaction(function () use ($user) {
            DB::selectOne("SELECT GET_LOCK('delete-admin-lock', 10)");

            try {
                $isTargetAdmin = $user->roles()
                    ->where('slug', 'admin')
                    ->exists();
                if ($isTargetAdmin) {
                    $adminCount = User::whereHas('roles', function ($query) {
                        $query->where('slug', 'admin');
                    })->count();

                    if ($adminCount <= 1) {
                        throw new \Exception('Cannot delete last admin.');
                    }
                }

                $user->delete();
            } finally {
                DB::selectOne("SELECT RELEASE_LOCK('delete-admin-lock')");
            }
        });
    }
}
