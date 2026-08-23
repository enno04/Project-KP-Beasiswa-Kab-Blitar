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

        // Global Web Settings
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('web_settings')) {
                $webSettings = \App\Models\WebSetting::pluck('value', 'key')->toArray();
                
                $contactPersons = [];
                if (isset($webSettings['contact_persons'])) {
                    $contactPersons = json_decode($webSettings['contact_persons'], true);
                }
                
                // Fallback jika kosong
                if (empty($contactPersons)) {
                    $contactPersons = [
                        ['name' => 'Bapak Akhyat', 'phone' => '0813-3400-1600']
                    ];
                }

                // Format nomor untuk link WhatsApp (ubah awalan 0 jadi 62)
                $contactPersons = array_map(function($cp) {
                    $phone = preg_replace('/[^0-9]/', '', $cp['phone'] ?? '');
                    if (str_starts_with($phone, '0')) {
                        $phone = '62' . substr($phone, 1);
                    }
                    $cp['wa_link'] = 'https://wa.me/' . $phone;
                    return $cp;
                }, $contactPersons);

                \Illuminate\Support\Facades\View::share('contactPersons', $contactPersons);

                // Share Global Settings
                \Illuminate\Support\Facades\View::share('webAddress', $webSettings['address'] ?? 'Jl. Raya Sawahan Pojok, Kec. Garum, Blitar');
                \Illuminate\Support\Facades\View::share('webMapsLink', $webSettings['maps_link'] ?? 'https://maps.app.goo.gl/Wgz7JqscQjiqs348A');
                \Illuminate\Support\Facades\View::share('webOperationalHours', $webSettings['operational_hours'] ?? 'Sen — Jum, 08:00 — 16:00 WIB');
                \Illuminate\Support\Facades\View::share('webInstagram', $webSettings['instagram_link'] ?? '');
                \Illuminate\Support\Facades\View::share('webLink', $webSettings['website_link'] ?? '');
                \Illuminate\Support\Facades\View::share('announcementActive', $webSettings['announcement_active'] ?? '0');
                \Illuminate\Support\Facades\View::share('announcementText', $webSettings['announcement_text'] ?? '');

            }
        } catch (\Exception $e) {
            // Do nothing if table does not exist
        }
    }
}
