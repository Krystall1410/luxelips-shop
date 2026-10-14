<?php

namespace App\Providers;

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
        if ($this->app->environment('production') || str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Mail::extend('brevo', function (array $config = []) {
            return new \App\Mail\Transport\BrevoTransport($config['key'] ?? env('BREVO_API_KEY', ''));
        });

        \Illuminate\Support\Facades\View::composer('user.*', function ($view) {
            if (!$view->offsetExists('categories')) {
                $view->with('categories', \App\Models\Category::where('is_active', true)
                    ->withCount(['variants as products_count' => function ($query) {
                        $query->whereHas('product', function ($q) {
                            $q->where('is_active', true);
                        })->select(\Illuminate\Support\Facades\DB::raw('count(distinct product_id)'));
                    }])
                    ->orderBy('name')
                    ->get());
            }
        });
    }
}
