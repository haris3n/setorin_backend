<?php

namespace App\Providers;

use App\Models\TransaksiPenyetoran;
use App\Observers\TransaksiPenyetoranObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observer
        TransaksiPenyetoran::observe(TransaksiPenyetoranObserver::class);

        // Configure rate limiting
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Rate limit untuk API - 60 request per menit
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limit untuk login - 5 attempt per menit
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate limit untuk register - 3 request per jam
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Rate limit untuk OTP - 3 request per menit
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }
}