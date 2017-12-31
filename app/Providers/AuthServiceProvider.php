<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Services\Auth\JwtGuard;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

//        Passport::routes();
//
        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JwtGuard($app, $name, Auth::createUserProvider($config['provider']));
        });

    }
}
