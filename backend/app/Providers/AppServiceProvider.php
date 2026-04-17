<?php

namespace App\Providers;

use App\Models\Playlist;
use App\Models\Release;
use ClickHouseDB\Client;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->singleton(Client::class, function () {

            $client = new Client([
                'host' => config('services.clickhouse.host'),
                'port' => config('services.clickhouse.port'),
                'username' => config('services.clickhouse.username'),
                'password' => config('services.clickhouse.password'),
            ]);

            $client->database(config('services.clickhouse.db'));

            return $client;
        });

        $this->app->singleton(StripeClient::class, function () {
            return new StripeClient(config('services.stripe.secret'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'playlist' => Playlist::class,
            'release' => Release::class,
        ]);
    }
}
