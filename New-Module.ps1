Param(
    [Parameter(Mandatory=$true)]
    [string]$ModuleName
)

$base = Join-Path $PSScriptRoot ".\app\Modules\$ModuleName"
$dirs = @("Http\Controllers","Http","UseCases","Infrastructure\Repositories","Http")
foreach ($d in $dirs) {
    $path = Join-Path $base $d
    New-Item -ItemType Directory -Force -Path $path | Out-Null
}

# create routes file
$routesFile = Join-Path $base "Http\routes.php"
@"
<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api','jwt.auth', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    // add module routes here
});
"@ | Set-Content -Path $routesFile -Encoding UTF8

Write-Host "Module $ModuleName scaffolded at $base"
