<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class MinioService
{
    public function storeCover($file): string
    {
        $path = Storage::disk('s3')->putFile('covers', $file);

        $url = Storage::disk('s3')->url($path);

        return str_replace('http://minio:9000', 'http://localhost:9000', $url);
    }

    public function storeProfile($file): string
    {
        $path = Storage::disk('s3')->putFile('profile_pictures', $file);

        $url = Storage::disk('s3')->url($path);

        return str_replace('http://minio:9000', 'http://localhost:9000', $url);
    }

    public function storeTrack($file): string
    {
        $fileName = Storage::disk('s3')->putFile('tracks', $file);

        $url = Storage::disk('s3')->url($fileName);

        return str_replace('http://minio:9000', 'http://localhost:9000', $url);
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
