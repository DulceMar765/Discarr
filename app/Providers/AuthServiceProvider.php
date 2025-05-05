<?php
namespace App\Providers;

use App\Models\Portfolio;
use App\Policies\PortfolioPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * El mapeo de políticas para los modelos de la aplicación.
     */
    protected $policies = [
        Portfolio::class => PortfolioPolicy::class,
    ];

    /**
     * Registra cualquier servicio de autenticación o autorización.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
