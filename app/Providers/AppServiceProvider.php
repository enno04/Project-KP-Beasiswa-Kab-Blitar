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

        // Global Web Settings (Menggunakan View Composer agar aman saat testing & migration)
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            static $webSettings = null;
            static $contactPersons = null;

            if (is_null($webSettings)) {
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('web_settings')) {
                        $webSettings = \App\Models\WebSetting::pluck('value', 'key')->toArray();
                        
                        $contactPersons = [];
                        if (isset($webSettings['contact_persons'])) {
                            $contactPersons = json_decode($webSettings['contact_persons'], true);
                        }
                        
                        if (empty($contactPersons)) {
                            $contactPersons = [
                                ['name' => 'Bapak Akhyat', 'phone' => '0813-3400-1600']
                            ];
                        }

                        $contactPersons = array_map(function($cp) {
                            $phone = preg_replace('/[^0-9]/', '', $cp['phone'] ?? '');
                            if (str_starts_with($phone, '0')) {
                                $phone = '62' . substr($phone, 1);
                            }
                            $cp['wa_link'] = 'https://wa.me/' . $phone;
                            return $cp;
                        }, $contactPersons);
                    } else {
                        // Fallback aman untuk Testing/Deployment awal
                        $webSettings = [];
                        $contactPersons = [
                            ['name' => 'Bapak Akhyat', 'phone' => '0813-3400-1600', 'wa_link' => 'https://wa.me/6281334001600']
                        ];
                    }
                } catch (\Exception $e) {
                    $webSettings = [];
                    $contactPersons = [
                        ['name' => 'Bapak Akhyat', 'phone' => '0813-3400-1600', 'wa_link' => 'https://wa.me/6281334001600']
                    ];
                }
            }

            $view->with('contactPersons', $contactPersons)
                 ->with('webAddress', $webSettings['address'] ?? 'Jl. Raya Sawahan Pojok, Kec. Garum, Blitar')
                 ->with('webMapsLink', $webSettings['maps_link'] ?? 'https://maps.app.goo.gl/Wgz7JqscQjiqs348A')
                 ->with('webOperationalHours', $webSettings['operational_hours'] ?? 'Sen — Jum, 08:00 — 16:00 WIB')
                 ->with('webInstagram', $webSettings['instagram_link'] ?? '')
                 ->with('webLink', $webSettings['website_link'] ?? '')
                 ->with('announcementActive', $webSettings['announcement_active'] ?? '0')
                 ->with('announcementText', $webSettings['announcement_text'] ?? '')
                 ->with('webSettings', $webSettings);
        });
    }
}
