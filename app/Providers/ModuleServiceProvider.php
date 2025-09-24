<?php

namespace App\Providers;

use App\Modules\DataUser\Infrastructure\Repositories\EloquentDataUserRepository as DataUserRepository;
use App\Modules\Forms\Infrastructure\Repositories\FormsRepository;
use App\Modules\Inventory\Infrastructure\Repositories\InventoryRepository;
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
use App\Modules\WorkLogs\Infrastructure\Repositories\WorkLogsRepository;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // raw materials
        $this->app->bind(RawMaterialRepositoryI::class, RawMaterialRepositoryE::class);
        $this->app->bind(RawMaterialReportService::class);

        // reports module
        $this->app->bind(ReportExportService::class);
        $this->app->bind(ReportAggregatorService::class);
        $this->app->bind(GenerateReportUseCase::class);

        $this->app->bind(ReportsRepository::class, fn ($app) => new ReportsRepository);
        $this->app->bind(FormsRepository::class, fn ($app) => new FormsRepository);
        $this->app->bind(WorkLogsRepository::class, fn ($app) => new WorkLogsRepository);
        $this->app->bind(InventoryRepository::class, fn ($app) => new InventoryRepository);
        $this->app->bind(UsersRepository::class, fn ($app) => new UsersRepository);
        $this->app->bind(NotificationsRepository::class, fn ($app) => new NotificationsRepository);
        $this->app->bind(RolesRepository::class, fn ($app) => new RolesRepository);
        $this->app->bind(DataUserRepository::class, fn ($app) => new DataUserRepository);
        // $this->app->bind(RawMaterialRepository::class, fn ($app) => new RawMaterialRepository);
    }

    public function boot()
    {
        //
    }
}
