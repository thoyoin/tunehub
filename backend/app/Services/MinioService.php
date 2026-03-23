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

        return str_replace('http://minio:9000', 'http://127.0.0.1:9000', $url);
    }

    public function storeMerchCover($file): string
    {
        $path = Storage::disk('s3')->putFile('merch_covers', $file);

        $url = Storage::disk('s3')->url($path);

        return str_replace('http://minio:9000', 'http://127.0.0.1:9000', $url);
    }

    public function storeProfile($file): string
    {
        $path = Storage::disk('s3')->putFile('profile_pictures', $file, [
            'Metadata' => [
                'filename' => $file->getClientOriginalName(),
            ],
        ]);

        $url = Storage::disk('s3')->url($path);

        return str_replace('http://minio:9000', 'http://127.0.0.1:9000', $url);
    }

    public function storeTrack($file): string
    {
        $fileName = Storage::disk('s3')->putFile('tracks', $file, [
            'Metadata' => [
                'filename' => $file->getClientOriginalName(),
            ],
        ]);

        $url = Storage::disk('s3')->url($fileName);

        return str_replace('http://minio:9000', 'http://127.0.0.1:9000', $url);
    }

    public function destroyTrack($audioPath): void
    {
        $parsedPath = parse_url($audioPath, PHP_URL_PATH);

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
