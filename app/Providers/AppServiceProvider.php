<?php

namespace App\Providers;

use App\Modules\Auth\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Auth\Infrastructure\Repositories\EloquentUserRepository;
use App\Modules\Batches\Domain\Repositories\BatcheRepositoryI;
use App\Modules\Batches\Infrastructure\Repositories\BatcheRepositoryE;
use App\Modules\DataUser\Domain\Repositories\DataUserRepositoryInterface;
use App\Modules\DataUser\Infrastructure\Repositories\EloquentDataUserRepository;
use App\Modules\InventoryMovements\Domain\Repositories\InvMoveRepositpyI;
use App\Modules\InventoryMovements\Infrastructure\Repositories\InvMoveRepositoyE;
use App\Modules\Notifications\Domain\Repositories\NotificationRepositoryI;
use App\Modules\Notifications\Infrastructure\Repositories\NotificationRepositoryE;
use App\Modules\RawMaterials\Domain\Repositories\RawMaterialRepositoryI;
use App\Modules\RawMaterials\Domain\Services\RawMaterialReportService;
use App\Modules\RawMaterials\Infrastructure\Repositories\RawMaterialRepositoryE;
use App\Modules\Reports\Application\UseCases\GenerateReportUseCase;
use App\Modules\Reports\Domain\Services\ReportAggregatorService;
use App\Modules\Reports\Domain\Services\ReportExportService;
use App\Modules\Roles\Domain\Repositories\RoleRepositoryInterface;
use App\Modules\Roles\Infrastructure\Repositories\EloquentRolesRepository;
use App\Modules\Users\Domain\Repositories\UsersRepositoryInterface;
use App\Modules\Users\Infrastructure\Repositories\EloquentUsersRepository;
use App\Modules\WorkLogs\Domain\Repositories\WorkLogRepositoryI;
use App\Modules\WorkLogs\Infrastructure\Repositories\WorkLogRepositoryE;
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
            InvMoveRepositpyI::class,
            InvMoveRepositoyE::class
        );

        $this->app->bind(
            WorkLogRepositoryI::class,
            WorkLogRepositoryE::class
        );

        $this->app->bind(
            NotificationRepositoryI::class,
            NotificationRepositoryE::class
        );
        // Servicios específicos por módulo
        $this->app->bind(RawMaterialReportService::class);
        // $this->app->bind(InventoryReportService::class);

        // Servicios del módulo Reports
        $this->app->bind(ReportExportService::class);
        $this->app->bind(ReportAggregatorService::class);
        $this->app->bind(GenerateReportUseCase::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scramble::configure()
            ->routes(function (Route $route) {
                return Str::startsWith($route->uri, 'api/');
            })
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer', 'JWT') // JWT Bearer
                );
            });
    }
}
