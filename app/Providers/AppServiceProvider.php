<?php

namespace App\Providers;

use App\Models\RawMaterial;
use App\Models\User;
use App\Modules\Auth\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Auth\Infrastructure\Repositories\EloquentUserRepository;
use App\Modules\Batches\Domain\Repositories\BatcheRepositoryI;
use App\Modules\Batches\Infrastructure\Repositories\BatcheRepositoryE;
use App\Modules\DataUser\Domain\Repositories\DataUserRepositoryInterface;
use App\Modules\DataUser\Infrastructure\Repositories\EloquentDataUserRepository;
use App\Modules\Forms\Domain\Repository\FormFieldRepositoryI;
use App\Modules\Forms\Domain\Repository\FormRepositoryI;
use App\Modules\Forms\Domain\Repository\FormResponseRepositoryI;
use App\Modules\Forms\Infrastructure\Repositories\FormFieldRepositoryE;
use App\Modules\Forms\Infrastructure\Repositories\FormRepositoryE;
use App\Modules\Forms\Infrastructure\Repositories\FormResponseRepositoryE;
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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        Scramble::ignoreDefaultRoutes();

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

        $this->app->bind(
            FormRepositoryI::class,
            FormRepositoryE::class
        );

        $this->app->bind(
            FormFieldRepositoryI::class,
            FormFieldRepositoryE::class
        );

        $this->app->bind(
            FormResponseRepositoryI::class,
            FormResponseRepositoryE::class
        );
        // Servicios específicos por módulo
        $this->app->bind(RawMaterialReportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 👀 Observador del modelo RawMaterial
        RawMaterial::observe(RawMaterialObserver::class);

        // 🚪 Definir gate para autorización de /docs/api
        Gate::define('viewApiDocs', function (User $user) {
            // Solo permite el acceso al correo de desarrollo
            return in_array($user->email, ['desarrollo@toliboy.com']);
        });

        // 🚫 Evita ejecutar Scramble en comandos artisan
        if ($this->app->runningInConsole()) {
            return;
        }

        // ⚙️ Configuración de Scramble para documentación API
        Scramble::configure()
            ->routes(function ($route) {
                // Solo incluir rutas que empiecen con "api/"
                return Str::startsWith($route->uri, 'api/');
            })
            ->withDocumentTransformers(function (OpenApi $openApi) {
                // Añadir esquema de seguridad JWT (Bearer Token)
                $openApi->secure(
                    SecurityScheme::http('bearer', 'JWT')
                );
            });
    }
}
