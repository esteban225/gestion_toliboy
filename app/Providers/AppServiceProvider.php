<?php

namespace App\Providers;

use App\Models\RawMaterial;
use App\Modules\Auth\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Auth\Infrastructure\Repositories\EloquentUserRepository;
use App\Modules\Batches\Domain\Repositories\BatcheRepositoryI;
use App\Modules\Batches\Infrastructure\Repositories\BatcheRepositoryE;
use App\Modules\DataUser\Domain\Repositories\DataUserRepositoryInterface;
use App\Modules\DataUser\Infrastructure\Repositories\EloquentDataUserRepository;
use App\Modules\InventoryMovements\Domain\Repositories\InvMoveRepositoryI;
use App\Modules\InventoryMovements\Infrastructure\Repositories\InvMoveRepositoryE;
use App\Modules\Notifications\Domain\Repositories\NotificationRepositoryI;
use App\Modules\Notifications\Infrastructure\Repositories\NotificationRepositoryE;
use App\Modules\Products\Domain\Repositories\ProductRepositoryI;
use App\Modules\Products\Infrastructure\Repositories\ProductRepositoryE;
use App\Modules\RawMaterials\Domain\Repositories\RawMaterialRepositoryI;
use App\Modules\RawMaterials\Domain\Services\RawMaterialReportService;
use App\Modules\RawMaterials\Infrastructure\Repositories\RawMaterialRepositoryE;
use App\Modules\Roles\Domain\Repositories\RoleRepositoryInterface;
use App\Modules\Roles\Infrastructure\Repositories\EloquentRolesRepository;
use App\Modules\Users\Domain\Repositories\UsersRepositoryInterface;
use App\Modules\Users\Infrastructure\Repositories\EloquentUsersRepository;
use App\Modules\WorkLogs\Domain\Repositories\WorkLogRepositoryI;
use App\Modules\WorkLogs\Infrastructure\Repositories\WorkLogRepositoryE;
use App\Observers\RawMaterialObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 👇 Aquí registras tu binding
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            EloquentRolesRepository::class
        );

        $this->app->bind(
            UsersRepositoryInterface::class,
            EloquentUsersRepository::class
        );

        $this->app->bind(
            DataUserRepositoryInterface::class,
            EloquentDataUserRepository::class
        );

        $this->app->bind(
            RawMaterialRepositoryI::class,
            RawMaterialRepositoryE::class
        );

        $this->app->bind(
            BatcheRepositoryI::class,
            BatcheRepositoryE::class
        );

        $this->app->bind(
            InvMoveRepositoryI::class,
            InvMoveRepositoryE::class
        );

        $this->app->bind(
            WorkLogRepositoryI::class,
            WorkLogRepositoryE::class
        );

        $this->app->bind(
            NotificationRepositoryI::class,
            NotificationRepositoryE::class
        );

        $this->app->bind(
            ProductRepositoryI::class,
            ProductRepositoryE::class
        );

        // Servicios específicos por módulo
        $this->app->bind(RawMaterialReportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RawMaterial::observe(RawMaterialObserver::class);

        if ($this->app->runningInConsole()) {
            return; // 👈 Evita ejecutar Scramble en comandos artisan
        }

        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/');
            })
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer', 'JWT')
                );
            });
    }
}
