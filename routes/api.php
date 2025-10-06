<?php

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
