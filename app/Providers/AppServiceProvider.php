<?php

namespace App\Providers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Policies\ArchivoPolicy;
use App\Policies\FolderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de modelos → policies.
     * Laravel lo usa automáticamente con $this->authorize() y authorizeResource().
     */
    protected $policies = [
        Carpeta::class => FolderPolicy::class,
        Archivo::class => ArchivoPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
         Schema::defaultStringLength(191);
    }
}