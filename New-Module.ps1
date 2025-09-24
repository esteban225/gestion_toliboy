Param(
    [Parameter(Mandatory=$true)]
    [string]$ModuleName
)

$base = Join-Path $PSScriptRoot ".\app\Modules\$ModuleName"

# Estructura de carpetas
$dirs = @(
    "Application\UseCases",
    "Domain\Entities",
    "Domain\Repositories",
    "Domain\Services",
    "Http\Controllers",
    "Http\Requests",
    "Infrastructure\Repositories"
)

foreach ($d in $dirs) {
    $path = Join-Path $base $d
    New-Item -ItemType Directory -Force -Path $path | Out-Null
}

# Crear archivos
foreach ($file in $files.Keys) {
    $filePath = Join-Path $base $file
    $folder = Split-Path $filePath -Parent
    if (!(Test-Path $folder)) {
        New-Item -ItemType Directory -Force -Path $folder | Out-Null
    }
    $files[$file] | Set-Content -Path $filePath -Encoding UTF8
}

Write-Host "✅ Módulo $ModuleName creado en $base"
