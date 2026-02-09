<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class MinioService
{
    public function storeCover($file): string
    {
        $path = Storage::disk('s3')->putFile('covers', $file);

        return Storage::disk('s3')->url($path);
    }

    public function storeProfile($file): string
    {
        $path = Storage::disk('s3')->putFile('profile_pictures', $file);

        return Storage::disk('s3')->url($path);
    }

    public function storeTrack($file): string
    {
        $fileName = Storage::disk('s3')->putFile('tracks', $file);

        return Storage::disk('s3')->url($fileName);
    }

    public function destroyTrack($file): void
    {

        $parsedPath = parse_url($file['audio_url'], PHP_URL_PATH);

        $cleanedPath = preg_replace('#^/tunehub#', '', $parsedPath);

        Storage::disk('s3')->delete($cleanedPath);
    }

    public function destroyCover($file): bool
    {
        $parsedPath = parse_url($file, PHP_URL_PATH);

        $cleanedPath = preg_replace('#^/tunehub#', '', $parsedPath);

        return Storage::disk('s3')->delete($cleanedPath);
    }
}
