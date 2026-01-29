<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes([
    'middleware' => ['jwt.auth'], // o el middleware exacto que uses
]);
/*
|--------------------------------------------------------------------------
| Load extra module routes
|--------------------------------------------------------------------------
*/
$moduleRouteFiles = glob(base_path('app/Modules/*/Http/routes.php'));
if ($moduleRouteFiles !== false) {
    foreach ($moduleRouteFiles as $file) {
        require $file;
    }
}
