<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Reports\Infrastructure\Repositories\ReportsRepository;
use App\Modules\Forms\Infrastructure\Repositories\FormsRepository;
use App\Modules\WorkLogs\Infrastructure\Repositories\WorkLogsRepository;
use App\Modules\Inventory\Infrastructure\Repositories\InventoryRepository;
use App\Modules\Users\Infrastructure\Repositories\EloquentUsersRepository as UsersRepository;
use App\Modules\Notifications\Infrastructure\Repositories\NotificationsRepository;
use App\Modules\Roles\Infrastructure\Repositories\EloquentRolesRepository as RolesRepository;
use App\Modules\DataUser\Infrastructure\Repositories\EloquentDataUserRepository as DataUserRepository;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ReportsRepository::class, fn($app) => new ReportsRepository());
        $this->app->bind(FormsRepository::class, fn($app) => new FormsRepository());
        $this->app->bind(WorkLogsRepository::class, fn($app) => new WorkLogsRepository());
        $this->app->bind(InventoryRepository::class, fn($app) => new InventoryRepository());
        $this->app->bind(UsersRepository::class, fn($app) => new UsersRepository());
        $this->app->bind(NotificationsRepository::class, fn($app) => new NotificationsRepository());
        $this->app->bind(RolesRepository::class, fn($app) => new RolesRepository());
        $this->app->bind(DataUserRepository::class, fn($app) => new DataUserRepository());
    }

    public function boot()
    {
        //
    }
}
