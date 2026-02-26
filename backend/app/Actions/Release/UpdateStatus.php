<?php

declare(strict_types=1);

namespace App\Actions\Release;

use App\Models\Release;
use Illuminate\Http\Request;

class UpdateStatus
{
    public function handle(Request $request, Release $release): void
    {
//        $request->validate([
//            'status' => 'required|in:pending, approved, rejected, published',
//        ]);

        $status = $request->input('status');

        $release->status = $status;

        $release->save();
    }
}
