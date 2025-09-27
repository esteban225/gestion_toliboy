<?php

namespace App\Providers;

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
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Auth
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);

        // Roles & Users
        $this->app->bind(RoleRepositoryInterface::class, EloquentRolesRepository::class);
        $this->app->bind(UsersRepositoryInterface::class, EloquentUsersRepository::class);

        // DataUser
        $this->app->bind(DataUserRepositoryInterface::class, EloquentDataUserRepository::class);

        // Raw Materials
        $this->app->bind(RawMaterialRepositoryI::class, RawMaterialRepositoryE::class);
        $this->app->bind(RawMaterialReportService::class);

        // Batches
        $this->app->bind(BatcheRepositoryI::class, BatcheRepositoryE::class);

        // Inventory Movements
        $this->app->bind(InvMoveRepositoryI::class, InvMoveRepositoryE::class);

        // WorkLogs
        $this->app->bind(WorkLogRepositoryI::class, WorkLogRepositoryE::class);

        // Notifications
        $this->app->bind(NotificationRepositoryI::class, NotificationRepositoryE::class);

        // Reports
        $this->app->bind(ReportExportService::class);
        $this->app->bind(ReportAggregatorService::class);
        $this->app->bind(GenerateReportUseCase::class);
    }

    public function boot(): void
    {
        //
    }
}
