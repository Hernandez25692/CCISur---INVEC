<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        // Forzar HTTPS y cookies seguras si la conexión viene por túnel (Expose/Ngrok) o proxy inverso HTTPS
        $isForwardedHttps = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
        $isDirectHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

        if ($isForwardedHttps || $isDirectHttps) {
            URL::forceScheme('https');
            config(['session.secure' => true]);
            if (isset($this->app['request'])) {
                $this->app['request']->server->set('HTTPS', 'on');
            }
        }

        // Puertas de autorización institucional
        Gate::define('admin', fn (User $user): bool => $user->esAdministrador());
        Gate::define('superadmin', fn (User $user): bool => $user->esSuperAdmin());
    }
}
