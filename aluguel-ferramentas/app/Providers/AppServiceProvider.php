<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // Ajuste para funcionar no Codespaces / ambiente local
        if (config('app.env') !== 'production') {
            URL::forceRootUrl(config('app.url'));

            if (str_starts_with((string) config('app.url'), 'https://')) {
                URL::forceScheme('https');
            }

            // Força a paginação a usar a URL pública do Codespaces
            Paginator::currentPathResolver(function () {
                return rtrim(config('app.url'), '/') . request()->getPathInfo();
            });
        }

        Blade::if('role', function ($roles) {
            $user = auth()->user();

            if (!$user) {
                return false;
            }

            $roles = is_array($roles) ? $roles : [$roles];

            return in_array($user->role, $roles);
        });
    }
}