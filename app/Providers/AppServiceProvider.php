<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS jika diakses melalui Ngrok atau koneksi yang di-forward HTTPS
        if (request()->header('x-forwarded-proto') === 'https' || str_contains(request()->getHost(), 'ngrok-free.app') || str_contains(request()->getHost(), 'ngrok.io')) {
            URL::forceScheme('https');
        }

        // Global Query Builder Macro for Searching
        \Illuminate\Database\Eloquent\Builder::macro('search', function ($attributes, ?string $searchTerm) {
            $this->where(function (\Illuminate\Database\Eloquent\Builder $query) use ($attributes, $searchTerm) {
                if ($searchTerm) {
                    foreach (\Illuminate\Support\Arr::wrap($attributes) as $attribute) {
                        $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                    }
                }
            });
            return $this;
        });

        // Global Query Builder Macro for Sorting
        \Illuminate\Database\Eloquent\Builder::macro('sort', function (?string $sortField, ?string $sortDir = 'asc') {
            if ($sortField) {
                $dir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';
                return $this->orderBy($sortField, $dir);
            }
            return $this;
        });
    }
}
