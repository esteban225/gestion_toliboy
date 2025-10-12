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
            # Gestión Toliboy

            ## 📋 Descripción General del Proyecto

            **Gestión Toliboy** es un sistema de gestión empresarial completo diseñado específicamente para empresas de producción, especialmente panaderías y pastelerías. Es una API REST robusta construida con Laravel 10 que implementa una **Arquitectura Limpia (Clean Architecture)** para garantizar escalabilidad, mantenibilidad y separación clara de responsabilidades.

            ---

            ## 🎯 Objetivos y Propósito

            El sistema está diseñado para digitalizar y automatizar completamente los procesos de una empresa de producción, eliminando el uso de papel y hojas de cálculo. Su objetivo principal es proporcionar:

            - **Trazabilidad completa** de todos los procesos productivos
            - **Control de calidad riguroso** mediante formularios dinámicos
            - **Gestión de inventario en tiempo real** con alertas automáticas
            - **Seguimiento de personal** con registros de entrada/salida automatizados
            - **Reportes ejecutivos** para toma de decisiones basada en datos
            - **Notificaciones automáticas** para eventos críticos del negocio

            ---

            ## 🏗️ Arquitectura del Sistema

            El proyecto implementa **Domain Driven Design (DDD)** con una estructura modular que separa claramente las responsabilidades:

            ### Capas de la Arquitectura

            1. **Capa de Presentación (HTTP)**: Controladores, requests de validación, recursos de respuesta
            2. **Capa de Aplicación**: Casos de uso específicos del negocio, DTOs, handlers de eventos
            3. **Capa de Dominio**: Entidades de negocio, interfaces de repositorio, servicios de dominio
            4. **Capa de Infraestructura**: Implementaciones con Eloquent, cache, colas, almacenamiento

            ### Módulos Principales

            - **Auth**: Manejo de autenticación JWT y autorización por roles
            - **WorkLogs**: Registro automático de asistencia y horas trabajadas
            - **Forms**: Sistema de formularios dinámicos para control de calidad
            - **Inventory**: Gestión completa de inventario y materias primas
            - **Notifications**: Sistema de notificaciones automáticas en tiempo real
            - **Reports**: Generación de reportes avanzados en múltiples formatos
            - **Batches**: Control de lotes de producción con trazabilidad completa

            ---

            ## 🚀 Funcionalidades Clave

            ### Sistema de Autenticación y Roles

            - Autenticación JWT con tokens de acceso y renovación
            - Cinco roles específicos: **Desarrollador (DEV)**, **Gerente General (GG)**, **Ingenieros (INGPL/INGPR)**, **Trazabilidad (TRZ)**, **Operarios (OP)**
            - Permisos granulares por módulo y acción
            - Sesiones activas con control de dispositivos múltiples

            ### Formularios Dinámicos Inteligentes

            - Creación de formularios completamente personalizables
            - Campos de múltiples tipos: texto, números, fechas, selecciones, archivos, checkboxes
            - Validación dinámica basada en reglas configurables
            - Workflow de aprobación: **Pendiente → En Progreso → Completado → Aprobado/Rechazado**
            - Vinculación automática a lotes de producción
            - Historial completo de cambios y revisiones

            ### Control de Asistencia Automatizado (WorkLogs)

            - Registro automático de entrada y salida de personal
            - Detección de ausencias con notificaciones automáticas a supervisores
            - Cálculo automático de horas trabajadas y extras
            - Alertas por exceso de horas trabajadas
            - Reportes de asistencia personalizables por período

            ### Gestión Inteligente de Inventario

            - Stock en tiempo real con actualizaciones automáticas
            - Alertas de bajo stock configurables por producto
            - Movimientos automáticos (entrada, salida, ajustes, transferencias)
            - Trazabilidad completa de materias primas en productos
            - Predicciones de consumo basadas en históricos
            - Integración con lotes de producción

            ### Sistema de Notificaciones Avanzado

            - Eventos automáticos disparados por cambios en el sistema
            - Múltiples canales: en aplicación, email, SMS (configurable)
            - Tipos de notificación: información, advertencia, crítica, urgente
            - Filtrado inteligente para evitar spam de notificaciones
            - Programación automática de verificaciones diarias

            ### Reportes Ejecutivos y Analytics

            - Dashboard interactivo con métricas clave en tiempo real
            - Reportes personalizables por fecha, usuario, lote, producto
            - Exportación múltiple: PDF, Excel, CSV
            - Gráficos de tendencias de producción, calidad, inventario
            - KPIs automáticos: eficiencia, defectos, rotación de inventario
            - Reportes programados con envío automático

            ---

            ## 🛠️ Tecnologías y Herramientas

            ### Stack Tecnológico

            - **Backend**: Laravel 10 con PHP 8.1+
            - **Base de Datos**: MySQL 8.0 con optimizaciones de rendimiento
            - **Autenticación**: JWT con refresh tokens
            - **Cache**: Redis para consultas frecuentes
            - **Colas**: Sistema de jobs para procesos pesados
            - **Storage**: Soporte para archivos locales y S3
            - **Testing**: PHPUnit y Pest para testing completo
            - **Documentación**: Scramble para documentación API automática

            ### Dependencias Especializadas

            - **Roles y Permisos**: Spatie Laravel Permission
            - **Exportación**: Maatwebsite Excel para reportes
            - **Imágenes**: Intervention Image para procesamiento
            - **Validación**: Validadores personalizados dinámicos
            - **Eventos**: Sistema robusto de events/listeners

            ---

            ## 📊 Flujo de Trabajo Típico

            1. Usuario se autentica con credenciales (JWT generado)
            2. Accede al dashboard con métricas según su rol
            3. Operario registra asistencia automáticamente
            4. Inicia lote de producción asignando formularios de calidad
            5. Durante producción completa formularios dinámicos
            6. Sistema valida automáticamente respuestas según reglas configuradas
            7. Movimientos de inventario se registran automáticamente
            8. Supervisor revisa y aprueba formularios completados
            9. Sistema genera notificaciones para eventos críticos
            10. Reportes automáticos se generan al final del día

            ---

            ## 🔒 Seguridad y Compliance

            ### Medidas de Seguridad

            - Autenticación JWT con expiración configurable
            - Rate limiting para prevenir ataques de fuerza bruta
            - Validación exhaustiva de todos los inputs
            - Sanitización de datos de salida
            - Logs de auditoría para todas las acciones críticas
            - Encriptación de datos sensibles
            - HTTPS obligatorio en producción

            ### Trazabilidad y Auditoría

            - Log completo de todas las acciones de usuarios
            - Historial de cambios en formularios y lotes
            - Rastreo de movimientos de inventario
            - Timestamps automáticos en todas las operaciones
            - Respaldos automáticos de base de datos

            ---

            ## 🎯 Beneficios para el Negocio

            ### Operacionales

            - Reducción del **90%** en uso de papel
            - Eliminación de errores de transcripción manual
            - Tiempo de respuesta **80% más rápido** en reportes
            - Trazabilidad **100% digital** de todos los procesos
            - Notificaciones inmediatas de problemas críticos

            ### Estratégicos

            - Datos en tiempo real para toma de decisiones
            - Cumplimiento automático de normas de calidad
            - Reducción de costos operativos significativa
            - Escalabilidad para crecimiento futuro
            - Integración fácil con otros sistemas

            ---

            ## 📝 Licencia

            Este proyecto es propiedad de Toliboy y está protegido por derechos de autor.

            ---

            ## 👥 Contacto

            Para más información sobre el proyecto Gestión Toliboy, contacta al equipo de desarrollo.
        ',
    ],

    /*
     * Personalización de la interfaz de la documentación (UI).
     */
    'ui' => [
        'title' => 'Toliboy API Docs', // Título que se muestra en la interfaz de la documentación.
        'theme' => 'dark', // Tema visual de la interfaz; en este caso, modo oscuro.
        'hide_try_it' => false, // Si es true, oculta el botón "Probar" en la documentación.
        'hide_schemas' => false, // Si es true, oculta la sección de esquemas de la API.
        'logo' => '/resources/img/carita.svg', // Ruta al logo personalizado que se muestra en la UI.
        'try_it_credentials_policy' => 'include', // Política de envío de credenciales (cookies, auth) en las pruebas de endpoints.
        'layout' => 'responsive', // Tipo de diseño de la interfaz: 'sidebar', 'responsive' o 'stacked'.
    ],

    /*
     * Servidores configurados para pruebas desde la doc.
     */
    'servers' => [
        'Producción' => 'https://api.toliboy.com/api',
        'Staging' => 'https://staging.toliboy.com/api',
        'AWS' => 'http://3.82.17.57/api',
        'Local' => 'http://127.0.0.1:8000/api',
        'docker' => 'http://localhost/api', // si usas docker y quieres probar desde otro contenedor    
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
        RestrictedDocsAccess::class,
        'auth', // puedes descomentar esto si quieres exigir login
        'can:viewApiDocs', // o usar permisos personalizados
    ],

    'extensions' => [],
];
