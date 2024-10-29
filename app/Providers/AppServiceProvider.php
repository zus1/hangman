<?php

namespace App\Providers;

use App\Eloquent\DiscriminatorBuilder;
use App\Http\Middleware\ApiAuth;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(ApiAuth::class)
            ->needs(Guard::class)
            ->give(function (Application $app) {
                /** @var AuthManager $authManager */
                $authManager = $app->make(AuthManager::class);

                return $authManager->guard('web');
            });

        //$this->app->bind(Builder::class, DiscriminatorBuilder::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
