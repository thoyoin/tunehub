<?php

use App\Http\Controllers\AdminPanel\MerchController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\ArtistMerchController;
use App\Http\Controllers\ArtistStudioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibraryItemController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\RecentlyPlayedController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'signIn');
        Route::post('/register', 'signUp');
    });
});

Route::post('/stripe/webhook', [\Laravel\Cashier\Http\Controllers\WebhookController::class, 'handleWebhook']);

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        Route::controller(\App\Http\Controllers\AdminPanel\UserController::class)->group(function () {
            Route::get('/users', 'getAll');
            Route::delete('/users/{user}/delete', 'delete');
            Route::get('/users/{user}', 'getDetails');
        });
        Route::controller(\App\Http\Controllers\AdminPanel\ReleaseController::class)->group(function () {
            Route::get('/releases', 'getReleases');
            Route::patch('/releases/{release}/status', 'updateStatus');
        });
        Route::controller(MerchController::class)->group(function () {
            Route::get('/merch', 'get');
            Route::patch('/merch/{merch}/status/update', 'updateStatus');
        });
        Route::controller(\App\Http\Controllers\AdminPanel\AnalyticsController::class)->group(function () {
            Route::get('/totalPlays', 'getTotalPlays');
            Route::get('/newUsers', 'getNewUsers');
            Route::get('/newTracks', 'getNewTracks');
            Route::get('/newReleases', 'getNewReleases');
            Route::get('/newPlaylists', 'getNewPlaylists');
            Route::get('/plays/month', 'getMonthPlays');
            Route::get('/userGrowth', 'getUserGrowth');
            Route::get('/topArtists', 'getTopArtists');
            Route::get('/topReleases', 'getTopReleases');
        });
    });

Route::controller(ArtistController::class)->group(function () {
    Route::get('/artist/{artist}/releases/latest', 'getLatestRelease');
    Route::get('/artist/{artist}', 'getArtist');
    Route::get('/artist/{artist}/tracks/top', 'getTopTracks');
    Route::get('/artist/{artist}/albums', 'getAlbums');
});

Route::controller(ReleaseController::class)->group(function () {
    Route::get('/release/{release}', 'show');
    Route::get('/releases/latest', 'getLatest');
});

Route::controller(SearchController::class)->group(function () {
    Route::get('/search', 'getContent');
    Route::get('/search/users', 'getUsers');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::controller(LibraryItemController::class)->group(function () {
        Route::get('/libraryItems/{id}', 'show');
    });

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
        Route::get('/artists/streams', 'getStreams');
        Route::get('/artists/streams/daily', 'getDailyStreams');
        Route::get('/artists/earnings', 'getEarnings');
        Route::get('/artists/top-tracks', 'getTopTracks');
        Route::get('/artists/top-releases', 'getTopReleases');
        Route::post('/artists/merch/drop', 'dropMerch');
        Route::get('/artists/merch', 'getMerch');
        Route::put('/artists/merch/{merch}/update', 'updateMerch');
        Route::delete('/artists/merch/{merch}/delete', 'deleteMerch');
        Route::patch('/artists/merch/{merch}/publish', 'publishMerch');
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

    Route::controller(SubscriptionController::class)->group(function () {
        Route::get('/user/billing-portal', 'goToBillingPortal');
        Route::post('/subscription/checkout', 'goToCheckout');
        Route::get('/subscription/details', 'getDetails');
    });

    Route::controller(ArtistMerchController::class)->group(function () {
       Route::get('/artist/merch/{slug}/get', 'get');
       Route::post('/artist/merch/checkout', 'goToCheckout');
    });
});

Route::controller(SubscriptionController::class)->group(function () {
    Route::get('/checkout/success', 'success')->name('checkout.success');
    Route::get('/checkout/cancel', 'cancel')->name('checkout.cancel');
});
