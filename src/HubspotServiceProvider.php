<?php

namespace Jordanbeattie\Hubspot;

use Illuminate\Support\Facades\Blade;
use Jordanbeattie\Hubspot\Components\OauthButton;
use Illuminate\Support\ServiceProvider;
use Jordanbeattie\Hubspot\Livewire\TeamSettings;
use Livewire\Livewire;

class HubspotServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Jordanbeattie\Hubspot\Controllers\HubspotController');
        $this->app->make('Jordanbeattie\Hubspot\Controllers\LoginController');
        $this->loadViewsFrom(__DIR__ . '/Views', 'hubspot');
        $this->loadViewComponentsAs('hubspot', [
            OauthButton::class
        ]);
        $this->app->bind('hubspot', function($app){
           return new Hubspot();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
        Blade::componentNamespace('Jordanbeattie\\Hubspot\\Components', 'hubspot');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        Livewire::component('hubspot-team-settings', TeamSettings::class);
    }
}
