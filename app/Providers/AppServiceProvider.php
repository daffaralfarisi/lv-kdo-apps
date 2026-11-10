<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        // Role admin: semua menu + master user. Role user: semua menu kecuali master user.
        Gate::define('admin', fn (User $user): bool => $user->isAdmin());

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by(
            Str::lower($request->string('kdo_email')->toString()).'|'.$request->ip(),
        ));
    }
}
