<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Release;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $libraryItems = $user ? $user
            ->libraryItems()
            ->with('item')
            ->get() : collect();
        $releases = Release::with('user')->get();

        $likedPlaylist = $libraryItems
            ->first()
            ->item ?? collect();

        return view('home', compact('user', 'libraryItems', 'releases', 'likedPlaylist'));
    }
}
