<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    /*
     * Ruta base de la API. Todas las rutas que comiencen con este prefijo se incluirán en la documentación.
     */
    'api_path' => 'api',

    /*
     * Dominio de la API. Si es null, se usará el dominio principal de la app.
     */
    'api_domain' => null,

    /*
     * Ruta donde se exportará el archivo OpenAPI (JSON).
     */
    'export_path' => 'api.json',

    'info' => [
        /*
         * Versión de la API.
         */
        'version' => env('API_VERSION', '1.0.0'),

        /*
         * Descripción mostrada en la página principal de la documentación.
         */
        'description' => '
            📘 Bienvenido a la documentación de la **API de Gestión Toliboy**.

            La API permite a aplicaciones móviles y servicios externos integrarse con el sistema de gestión,
            ofreciendo funcionalidades clave como:

            - 👤 **Autenticación y gestión de usuarios**
            - 📦 **Productos y categorías**
            - 🛒 **Pedidos y facturación electrónica**
            - 📊 **Reportes y estadísticas**

            ⚠️ **Nota importante:**
            - Todos los endpoints protegidos requieren autenticación vía **Bearer Token (JWT)**.
            - Revisa cada sección para ver parámetros, ejemplos de respuestas y posibles errores.

            👉 Si necesitas ayuda, contacta con el equipo de soporte de Toliboy.
        ',
    ],

    /*
     * Personalización de la interfaz de la documentación (UI).
     */
    'ui' => [
        'title' => 'Toliboy API Docs',
        'theme' => 'dark',
        'hide_try_it' => false,
        'hide_schemas' => false,
        'logo' => '/resources/img/carita.svg', // cambia por la ruta real de tu logo
        'try_it_credentials_policy' => 'include',
        'layout' => 'sidebar', // opciones: sidebar | responsive | stacked
    ],

    /*
     * Servidores configurados para pruebas desde la doc.
     */
    'servers' => [
        'Local' => 'http://127.0.0.1:8000/api',
        'Staging' => 'https://staging.toliboy.com/api',
        'Producción' => 'https://toliboy.com/api',
        'docker' => 'http://localhost/api', // si usas docker y quieres probar desde otro contenedor
        'AWS' => 'http://18.188.114.143/api'
    ],

    /*
     * Estrategia para mostrar las descripciones de enums.
     */
    'enum_cases_description_strategy' => 'description',

    /*
     * Middleware que protege la ruta de los docs.
     */
    'middleware' => [
        'web',
        //RestrictedDocsAccess::class, 
        // 'auth', // puedes descomentar esto si quieres exigir login
        // 'can:viewDocs', // o usar permisos personalizados
    ],

    'extensions' => [],
];
