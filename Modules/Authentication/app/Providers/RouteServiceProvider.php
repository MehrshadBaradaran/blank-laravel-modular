<?php

namespace Modules\Authentication\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'Modules\Authentication\app\Http\Controllers';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->as('web.')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Authentication', '/Routes/Web/web.php'));
    }

    protected function mapApiRoutes(): void
    {
        $attributes = [
            'prefix' => 'api',
            'as' => 'api.',
            'middleware' => ['api',],
            'namespace' => $this->moduleNamespace,
        ];

        Route::group($attributes, function () {
            $path = module_path('Authentication', 'Routes/Api/');

            // V1
            Route::group(['prefix' => 'v1', 'as' => 'v1.',], function () use ($path) {
                Route::prefix('auth')
                    ->as('auth.')
                    ->group($path . 'v1/api.php');
            });
        });
    }
}
