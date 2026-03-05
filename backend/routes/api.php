<?php

use App\Http\Controllers\ArtistStudioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibraryItemController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\RecentlyPlayedController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'signIn');
        Route::post('/register', 'signUp');
    });
});

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        Route::controller(\App\Http\Controllers\AdminPanel\UserController::class)->group(function () {
            Route::get('/users', 'getAll');
            Route::delete('/users/{user}/delete', 'delete');
        });
        Route::controller(\App\Http\Controllers\AdminPanel\ReleaseController::class)->group(function () {
            Route::get('/releases', 'getReleases');
            Route::patch('/releases/{release}/status', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\AdminPanel\PlaylistController::class)->group(function () {
            Route::get('/playlists', 'getAll');
            Route::patch('/playlists/{playlist}/status', 'updateStatus');
        });
    });

Route::controller(ReleaseController::class)->group(function () {
    Route::get('/release/{release}', 'show');
    Route::get('/releases/latest', 'getLatest');
});

Route::controller(SearchController::class)->group(function () {
    Route::get('/search', 'getContent');
    Route::get('/search/users', 'getUsers');
});

Route::controller(LibraryItemController::class)->group(function () {
    Route::get('/libraryItems/{id}', 'show');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'get');
        Route::post('/user/update', 'update');
    });

    Route::controller(PlaylistController::class)->group(function () {
        Route::post('/playlist', 'store');
        Route::put('/playlist/{playlist}', 'update');
        Route::delete('/playlist/{playlist}', 'destroy');
        Route::get('/playlist/{playlist}', 'show');
        Route::get('/playlists', 'getAll');
        Route::post('/playlist/{playlist}/track/{track}', 'addTrack');
        Route::post('/liked/track/{track}', 'addTrackToLikes');
        Route::patch('/playlist/{playlist}', 'updateVisibility');
    });

    Route::controller(RecentlyPlayedController::class)->group(function () {
        Route::post('/recentlyPlayed', 'store');
        Route::get('/recentlyPlayed', 'get');
    });

    Route::controller(LibraryItemController::class)->group(function () {
        Route::get('/libraryItems', 'getAll');
    });

    Route::controller(ArtistStudioController::class)->group(function () {
        Route::get('/artists/tracks', 'getTracks');
        Route::get('/artists/releases', 'getReleases');
    });

    Route::controller(TrackController::class)->group(function () {
        Route::post('/track', 'store');
        Route::put('/track/{track}', 'update');
        Route::delete('/track/{track}', 'destroy');
    });

    Route::controller(ReleaseController::class)->group(function () {
        Route::delete('/release/{release}', 'destroy');
        Route::patch('/release/{release}/publish', 'publish');
        Route::post('/releases/{release}/add', 'addToLikes');
        Route::put('/releases/{release}', 'update');
    });
});
