<?php

declare(strict_types=1);

namespace App\Actions\Release;

use App\Models\Release;
use App\Models\User;
use App\Notifications\ReleaseApproved;
use App\Notifications\ReleaseRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateStatus
{
    public function handle(Request $request, Release $release): void
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,published',
        ]);

        $status = $request->input('status');

        $user = User::where('id', $release->user_id)
            ->firstOrFail();

        if ($status === 'approved') {
            $user->notify(new ReleaseApproved($release));
        } else if ($status === 'rejected') {
            $user->notify(new ReleaseRejected($release));
        }

        $release->status = $status;

        $release->save();

        Log::info('Moderation: release status was updated', [
            'release' => $release->title,
            'status' => $release->status,
        ]);
    }
}
