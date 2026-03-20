<?php

namespace App\Providers;

use App\Models\Playlist;
use App\Models\Release;
use ClickHouseDB\Client;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

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
            $this->app->singleton(Client::class, function () {
                $client = new Client([
                    'host' => env('CLICKHOUSE_HOST', 'tunehub-clickhouse-1'),
                    'port' => env('CLICKHOUSE_PORT', 8123),
                    'username' => env('CLICKHOUSE_USER', 'default'),
                    'password' => env('CLICKHOUSE_PASSWORD', 'default'),
                ]);

                $client->database(env('CLICKHOUSE_DB', 'default'));

                return $client;
            });
        }
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
