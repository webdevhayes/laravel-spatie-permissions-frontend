<?php

namespace Webdevhayes\LaravelSpatiePermissionsFrontend;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LaravelSpatiePermissionsFrontendServiceProvider extends ServiceProvider
{

    public function register()
    {
        if ($this->app->runningInConsole()) {

            $this->commands([
                LaravelSpatiePermissionsFrontendCommand::class,
                LaravelSpatiePermissionsFrontendControllersCommand::class,
            ]);
        }
    }

}
