<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación API - Gestión Toliboy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #000000;
            overflow: hidden;
        }

        header {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: rgba(15, 15, 15, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid #ff0000;
            box-shadow:
                0 4px 20px rgba(255, 0, 0, 0.3),
                0 0 40px rgba(255, 0, 0, 0.1);
            z-index: 1000;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow:
                0 0 15px rgba(255, 0, 0, 0.6),
                0 0 30px rgba(255, 0, 0, 0.3);
            animation: logoGlow 2s ease-in-out infinite;
        }

        @keyframes logoGlow {

            0%,
            100% {
                box-shadow:
                    0 0 15px rgba(255, 0, 0, 0.6),
                    0 0 30px rgba(255, 0, 0, 0.3);
            }

            50% {
                box-shadow:
                    0 0 25px rgba(255, 0, 0, 0.8),
                    0 0 50px rgba(255, 0, 0, 0.5);
            }
        }

        .logo-icon svg {
            width: 24px;
            height: 24px;
            fill: white;
            filter: drop-shadow(0 0 3px rgba(255, 255, 255, 0.8));
        }

        .logo-text h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 0 10px rgba(255, 0, 0, 0.8);
            line-height: 1;
            margin-bottom: 2px;
        }

        .logo-text p {
            font-size: 0.75rem;
            color: #ff6666;
            text-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
            line-height: 1;
        }

        .header-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.3);
            border-radius: 8px;
            margin-left: 2rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #00ff00;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
            animation: statusPulse 2s ease-in-out infinite;
        }

        @keyframes statusPulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.2);
            }
        }

        .status-text {
            font-size: 0.875rem;
            color: #00ff00;
            text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 0, 0, 0.05);
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
        }

        .user-avatar svg {
            width: 18px;
            height: 18px;
            fill: white;
        }

        .user-name {
            font-size: 0.875rem;
            color: #ffffff;
            font-weight: 500;
        }

        .logout-form {
            margin: 0;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
            color: white;
            border: 2px solid #ff0000;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            box-shadow:
                0 0 15px rgba(255, 0, 0, 0.5),
                0 0 30px rgba(255, 0, 0, 0.2);
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow:
                0 0 25px rgba(255, 0, 0, 0.7),
                0 0 50px rgba(255, 0, 0, 0.4);
            background: linear-gradient(135deg, #ff1a1a 0%, #e60000 100%);
        }

        .btn-logout:active {
            transform: translateY(0);
        }

        .btn-logout svg {
            width: 16px;
            height: 16px;
            fill: white;
        }

        .main-container {
            position: relative;
            height: calc(100vh - 72px);
            background: #000000;
        }

        .iframe-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .iframe-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg,
                    transparent 0%,
                    #ff0000 50%,
                    transparent 100%);
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
            animation: scanLine 3s linear infinite;
        }

        @keyframes scanLine {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            background: #ffffff;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #000000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 999;
            opacity: 1;
            transition: opacity 0.5s ease;
        }

        .loading-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255, 0, 0, 0.1);
            border-top: 3px solid #ff0000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.5);
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            margin-top: 1.5rem;
            color: #ff6666;
            font-size: 1rem;
            font-weight: 500;
            text-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .header-left {
                width: 100%;
                justify-content: space-between;
            }

            .header-status {
                margin-left: 0;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .user-info {
                flex: 1;
            }

            .logo-text h1 {
                font-size: 1rem;
            }

            .logo-text p {
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-left">
            <div class="logo-container">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-4.41 0-8-3.59-8-8V8.41l8-4 8 4V12c0 4.41-3.59 8-8 8zm-1-6h2v2h-2v-2zm0-8h2v6h-2V6z" />
                    </svg>
                </div>
                <div class="logo-text">
                    <h1>Gestión Toliboy</h1>
                    <p>Documentación API v1.0</p>
                </div>
            </div>

            <div class="header-status">
                <div class="status-dot"></div>
                <span class="status-text">API Activa</span>
            </div>
        </div>

        <div class="header-right">
            <div class="user-info">
                <div class="user-avatar">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
                <span class="user-name">Usuario Autenticado</span>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
                    </svg>
                    Cerrar Sesión
                </button>
            </form>

        </div>
    </header>

    <div class="main-container">
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loader"></div>
            <div class="loading-text">Cargando documentación...</div>
        </div>

        <div class="iframe-wrapper">
            <iframe src="/docs/api" title="Documentación API" id="apiDocs" onload="hideLoading()"></iframe>
        </div>
    </div>

    <script>
        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 500);
        }

        // Ocultar loading después de 3 segundos como fallback
        setTimeout(hideLoading, 3000);
    </script>
</body>

</html>
