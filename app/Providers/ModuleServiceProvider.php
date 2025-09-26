<?php

namespace App\Providers;

use App\Modules\Batches\Domain\Repositories\BatcheRepositoryI;
use App\Modules\Batches\Infrastructure\Repositories\BatcheRepositoryE;
use App\Modules\DataUser\Infrastructure\Repositories\EloquentDataUserRepository as DataUserRepository;
use App\Modules\Forms\Infrastructure\Repositories\FormsRepository;
use App\Modules\Inventory\Infrastructure\Repositories\InventoryRepository;
use App\Modules\InventoryMovements\Domain\Repositories\InvMoveRepositpyI;
use App\Modules\InventoryMovements\Infrastructure\Repositories\InvMoveRepositoyE;
use App\Modules\Notifications\Domain\Repositories\NotificationRepositoryI;
use App\Modules\Notifications\Infrastructure\Repositories\NotificationRepositoryE;
use App\Modules\Notifications\Infrastructure\Repositories\NotificationsRepository;
use App\Modules\RawMaterials\Domain\Repositories\RawMaterialRepositoryI;
use App\Modules\RawMaterials\Domain\Services\RawMaterialReportService;
use App\Modules\RawMaterials\Infrastructure\Repositories\RawMaterialRepositoryE;
use App\Modules\Reports\Application\UseCases\GenerateReportUseCase;
use App\Modules\Reports\Domain\Services\ReportAggregatorService;
use App\Modules\Reports\Domain\Services\ReportExportService;
use App\Modules\Reports\Infrastructure\Repositories\ReportsRepository;
use App\Modules\Roles\Infrastructure\Repositories\EloquentRolesRepository as RolesRepository;
use App\Modules\Users\Infrastructure\Repositories\EloquentUsersRepository as UsersRepository;
use App\Modules\WorkLogs\Domain\Repositories\WorkLogRepositoryI;
use App\Modules\WorkLogs\Infrastructure\Repositories\WorkLogRepositoryE;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->bind(RawMaterialRepositoryI::class, RawMaterialRepositoryE::class);
        $this->app->bind(BatcheRepositoryI::class, BatcheRepositoryE::class);
        $this->app->bind(InvMoveRepositpyI::class, InvMoveRepositoyE::class);
        $this->app->bind(WorkLogRepositoryI::class, WorkLogRepositoryE::class);
        $this->app->bind(NotificationRepositoryI::class, NotificationRepositoryE::class);
        $this->app->bind(RawMaterialReportService::class);

        // reports module
        $this->app->bind(ReportExportService::class);
        $this->app->bind(ReportAggregatorService::class);
        $this->app->bind(GenerateReportUseCase::class);

        $this->app->bind(ReportsRepository::class, fn ($app) => new ReportsRepository);
        $this->app->bind(FormsRepository::class, fn ($app) => new FormsRepository);

        $this->app->bind(InventoryRepository::class, fn ($app) => new InventoryRepository);
        $this->app->bind(UsersRepository::class, fn ($app) => new UsersRepository);
        $this->app->bind(RolesRepository::class, fn ($app) => new RolesRepository);
        $this->app->bind(DataUserRepository::class, fn ($app) => new DataUserRepository);
        // $this->app->bind(RawMaterialRepository::class, fn ($app) => new RawMaterialRepository);
    }

    public function boot()
    {
        //
    }
}
