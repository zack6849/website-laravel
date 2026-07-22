<?php

namespace App\Providers;

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;
use Livewire\Livewire;
use Mockery;
use Sentry\Laravel\ServiceProvider as SentryServiceProvider;
use Sentry\Laravel\Tracing\ServiceProvider as SentryTracingServiceProvider;
use Twilio\Rest\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('production')) {
            $this->app->register(SentryServiceProvider::class);
            $this->app->register(SentryTracingServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Livewire::addPersistentMiddleware(EnsureUserIsAdmin::class);

        $this->app->bind(Client::class, function () {
            return new Client(config('twilio.sid'), config('twilio.token'));
        });

        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('access-admin', function (User $user) {
            return $user->isAdmin();
        });
    }
}
