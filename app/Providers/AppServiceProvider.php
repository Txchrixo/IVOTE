<?php

namespace App\Providers;

use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Password policy used by every password form
        Password::defaults(function () {
            $rule = Password::min(12)->letters()->mixedCase()->numbers()->symbols();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        view()->composer('templates.partials.language_switcher', function ($view) {
            $view->with('current_locale', app()->getLocale());
            $view->with('available_locales', config('app.available_locales'));
        });

        Vite::macro('image', fn (string $asset) => $this->asset("resources/img/{$asset}"));
        Vite::macro('bg', fn (string $asset) => $this->asset("storage/app/public/uploads/bg/{$asset}"));
    }
}
