<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\merch;

use App\Models\Product;
use App\Models\User;
use App\Notifications\MerchApproved;
use App\Notifications\MerchRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateMerchStatus
{
    public function handle(Request $request, Product $merch): void
    {
        $request->validate([
            'status' => 'required|in:moderating,approved,rejected'
        ]);

        $status = $request->input('status');

        $merch->update(['status' => $status]);

        $merch->save();

        $artist = User::where('id', $merch->user_id)
            ->firstOrFail();

        if ($status === 'approved') {
            $artist->notify(new MerchApproved($merch));
        } else if ($status === 'rejected') {
            $artist->notify(new MerchRejected($merch));
        }

        Log::info('Moderation: merch status was updated', [
            'merch' => $merch->title,
            'status' => $merch->status,
        ]);
    }
}
