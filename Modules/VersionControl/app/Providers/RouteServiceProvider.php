<?php

namespace Modules\VersionControl\app\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $moduleNamespace = 'Modules\VersionControl\app\Http\Controllers';

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
        $path = module_path('VersionControl', '/Routes/Web/');
        $attributes = [
            'as' => 'web.version-control.',
            'prefix' => 'version-control',
            'middleware' => ['web', 'auth:web',],
            'namespace' => $this->moduleNamespace,
        ];

        Route::group($attributes, function () use ($path) {
            // Admin Panel
            Route::prefix('admin-panel')
                ->as('admin-panel.')
                ->middleware(['panel.admin.access',])
                ->group($path . 'admin_panel.php');

            // App
            Route::as('app.')
                ->group($path . 'app.php');
        });
    }

    protected function mapApiRoutes(): void
    {
        $attributes = [
            'prefix' => 'api',
            'as' => 'api.',
            'middleware' => ['api', 'auth:api',],
            'namespace' => $this->moduleNamespace,
        ];

        Route::group($attributes, function () {
            $path = module_path('VersionControl', 'Routes/Api/');

            // V1
            Route::group(['prefix' => 'v1', 'as' => 'v1.',], function () use ($path) {
                // Admin Panel
                Route::prefix('admin-panel/version-control')
                    ->as('admin-panel.version-control.')
                    ->middleware(['panel.admin.access',])
                    ->group($path . 'v1/admin_panel.php');

                // App
                Route::prefix('app/version-control')
                    ->as('app.version-control.')
                    ->group($path . 'v1/app.php');
            });
        });
    }
}
