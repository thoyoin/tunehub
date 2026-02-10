<?php

use App\Http\Controllers\ArtistStudioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryItemController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'signIn');
        Route::post('/register', 'signUp');
    });
});

Route::controller(ReleaseController::class)->group(function () {
    Route::get('/release/{release}', 'show');
    Route::get('/releases/latest', 'getLatest');
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

    Route::controller(HomeController::class)->group(function () {
        Route::get('/releases', 'index');
    });

    Route::controller(PlaylistController::class)->group(function () {
        Route::post('/playlist', 'store');
        Route::put('/playlist/{playlist}', 'update');
        Route::delete('/playlist/{playlist}', 'destroy');
        Route::get('/playlist/{playlist}', 'show');
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
        Route::delete('/track/{track}', 'destroy');
        Route::post('/tracks/{track}/add', 'addToLikes');
        Route::put('/track/{track}', 'update');
    });

    Route::controller(ReleaseController::class)->group(function () {
        Route::delete('/release/{release}', 'destroy');
        Route::post('/releases/{release}/add', 'addToLikes');
        Route::put('/releases/{release}', 'update');
    });
});
