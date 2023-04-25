<?php

namespace App\Providers;

use App\Services\Implementations\NullUserModificationLog;
use App\Services\Implementations\TestProhibitedWordsList;
use App\Services\Implementations\TestTrustedDomains;
use App\Services\Interfaces\ProhibitedWordsList;
use App\Services\Interfaces\TrustedDomains;
use App\Services\Interfaces\UserModificationLog;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            UserModificationLog::class,
            NullUserModificationLog::class
        );
        $this->app->singleton(
            ProhibitedWordsList::class,
            TestProhibitedWordsList::class
        );
        $this->app->singleton(
            TrustedDomains::class,
            TestTrustedDomains::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
