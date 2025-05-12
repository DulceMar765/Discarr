<?php
namespace App\Providers;

use App\Models\Portfolio;
use App\Models\Category;
use App\Models\Employee;
use App\Policies\PortfolioPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\EmployeePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * El mapeo de políticas para los modelos de la aplicación.
     */
    protected $policies = [
        Portfolio::class => PortfolioPolicy::class,
        Category::class => CategoryPolicy::class,
        Employee::class  => EmployeePolicy::class, 
    ];

    /**
     * Registra cualquier servicio de autenticación o autorización.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
