# Gestion Toliboy

<div align="center">
  <h1>Gestion Toliboy</h1>
  <p>Backend Laravel 12 para gestión de producción, lotes, inventario, formularios y control horario.</p>
  <p>
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square" alt="Laravel">
    <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square" alt="PHP">
    <img src="https://img.shields.io/badge/DB-MySQL%20/SQLite-4479A1?style=flat-square" alt="DB">
    <img src="https://img.shields.io/badge/JWT-Auth-000000?style=flat-square" alt="JWT">
    <img src="https://img.shields.io/badge/Soketi-WebSockets-4ea8de?style=flat-square" alt="Soketi">
  </p>
</div>

---

## Resumen rápido
API RESTful con:
- Autenticación JWT y control por roles (DEV, GG, INPL, INPR, TRZ, OP).
- Gestión de materias primas, productos, lotes, movimientos de inventario.
- Formularios dinámicos y registro de respuestas.
- Work logs (registro de jornada) con cálculo de horas extras.
- Exportes de reportes: CSV, PDF, XLSX (maatwebsite/excel).
- Integración WebSockets (Soketi) para features en tiempo real.

## Requisitos (entorno de desarrollo)
- PHP 8.2+ (ver nota en Composer para PHP 8.3)
- Composer
- Node.js + npm
- MySQL o SQLite
- Opcionales (para exportes): ext-zip, ext-gd, ext-mbstring, ext-intl
- Paquetes PHP opcionales:
  - dompdf/dompdf (PDF export)
  - maatwebsite/excel (XLSX export)

## Instalación (rápida, Windows / PowerShell)
Importante: si tu entorno usa PHP 8.3 sigue la regla de Composer indicada más abajo.

1. Clonar y entrar al proyecto
```powershell
git clone <repo-url>
cd gestion_toliboy
```

2. Dependencias PHP (IMPORTANTE para PHP 8.3)
```powershell
# En un entorno nuevo ejecutar:
composer update --no-interaction    # toma 5-6 minutos, NO CANCELAR (timeout 10+ min)
composer install --no-interaction
```

3. Dependencias Node
```powershell
npm install
npm audit fix
```

4. Variables de entorno
```powershell
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

5. Base de datos
- SQLite (rápido para desarrollo):
```powershell
New-Item -Path database -Name database.sqlite -ItemType File
# y en .env: DB_CONNECTION=sqlite DB_DATABASE=database/database.sqlite
php artisan session:table
php artisan migrate
```
- MySQL: importar el SQL maestro o ejecutar migraciones después de configurar .env:
```powershell
# Importar SQL (ejemplo)
mysql -u root -p ftoliboy_toliboy_data < db.info/ftoliboy_toliboy_data.sql
```

6. Seeders (roles y usuario dev)
```powershell
php artisan db:seed
```
Usuario por defecto: dev@example.com / password (cambiar en prod).

7. Build frontend (producción)
```powershell
npm run build
```

## Ejecución
- Servidor local:
```powershell
php artisan serve --host=0.0.0.0 --port=8000
```
Acceso API base: http://localhost:8000/api

- En desarrollo con recarga (Vite):
establecer `LARAVEL_BYPASS_ENV_CHECK=1` en .env y seguir tu flujo de dev (ver instrucciones de front).

## Endpoints importantes (resumen)
- POST /api/auth/login — obtener JWT
- GET /api/me — usuario autenticado
- GET /api/db/dashboard — vistas principales (roles: DEV, GG, INPL, INPR)
- GET /api/reports/{reportName} — obtener reporte por nombre (GG, INPL, INPR, DEV)
- GET /api/reports/{reportName}/export?format=csv|pdf|xlsx — exportar reporte
- POST /api/form-responses — operarios: guardar respuestas de formularios
- POST /api/work-logs — operarios: registrar jornada
- Rutas CRUD para recursos según rol (ver routes/api.php)

## Roles y permisos (comportamiento)
- DEV: administración total (users, roles, todos los recursos).
- GG (Gerente General): vistas/estadísticas, notificaciones y widgets del front.
- INPL / INPR (Ingenieros): acceso a estadísticas, formularios, work-logs.
- TRZ (Trazabilidad): acceso lectura a informes, formularios y reportes.
- OP (Operarios): diligenciamiento de formularios y registro de horas.

## Exportes y librerías en producción
- XLSX requiere maatwebsite/excel:
  composer require maatwebsite/excel
- PDF requiere dompdf:
  composer require dompdf/dompdf
- Verificar extensiones PHP en servidor (zip, gd, mbstring, intl).

Importante en producción:
- Usar composer.lock; NO ejecutar `composer update` en prod.
- En deploy:
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
- Para exportes grandes: usar jobs en background (queues) en lugar de procesamiento sincrónico.

## Testing & Calidad
- Tests: `php artisan test` (Pest)
- Formateo: `vendor/bin/pint --test` y `vendor/bin/pint`
- CI: Ejecutar Pint antes de commitear para evitar fallos en CI.

## Broadcasting / WebSockets
- Configurar Soketi (Docker o NPM). Variables en .env:
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```
- Asegurar /broadcasting/auth protegido por JWT para canales privados/presence.

## Triggers y sincronización de stock
- Inventory_movements ↔ raw_materials.stock: preferible mantener triggers o hacer actualizaciones en transacción desde controllers. Ver db.info/ftoliboy_toliboy_data.sql para triggers sugeridos.

## Troubleshooting rápido
- Composer en PHP 8.3: ejecutar `composer update` localmente y comprometer `composer.lock`.
- Faltan tablas → importar SQL maestro o ejecutar migraciones.
- Exports fallando → instalar las librerías requeridas y comprobar extensiones PHP.
- WebSockets → verificar Soketi en ejecución y configuración de .env.

## Checklist de despliegue breve
- [ ] composer install --no-dev --optimize-autoloader
- [ ] Migraciones ejecutadas (php artisan migrate --force)
- [ ] Seeders ejecutados (si es necesario)
- [ ] Workers/queues configurados (supervisor/systemd)
- [ ] Soketi o servicio WebSocket en producción
- [ ] Configuración TLS y secretos revisados

---

Hecho para facilitar desarrollo y despliegue. Ajusta las vistas por defecto y los report names en `ReportsController` según tu esquema real.
