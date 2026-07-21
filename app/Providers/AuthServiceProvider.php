<?php

namespace App\Providers;

use App\Auth\LegacyCompatibleTokenGuard;
use App\Models\File;
use App\Policies\FilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        File::class => FilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend('legacy-compatible-token', function ($app, string $name, array $config): LegacyCompatibleTokenGuard {
            $guard = new LegacyCompatibleTokenGuard(
                $app['auth']->createUserProvider($config['provider'] ?? null),
                $app['request'],
                $config['input_key'] ?? 'api_token',
                $config['storage_key'] ?? 'api_token',
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}
