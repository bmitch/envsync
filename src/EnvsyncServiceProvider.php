<?php

namespace Bmitch\Envsync;

use Illuminate\Support\ServiceProvider;

class EnvsyncServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands('Bmitch\Envsync\SyncerCommand');
    }
}
