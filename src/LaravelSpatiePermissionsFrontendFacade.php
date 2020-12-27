<?php

namespace Webdevhayes\LaravelSpatiePermissionsFrontend;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Webdevhayes\LaravelSpatiePermissionsFrontend\LaravelSpatiePermissionsFrontend
 */
class LaravelSpatiePermissionsFrontendFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-spatie-permissions-frontend';
    }
}
