<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Toliboy - Acceso a Documentación API</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f4f8fb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59,130,246,0.04) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
        }

        body::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.13) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.8; transform: translate(-50%, -50%) scale(1.1); }
        }

        .login-container {
            background: rgba(230, 242, 255, 0.97);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            border: 2px solid #3b82f6;
            box-shadow: 
                0 0 20px rgba(59, 130, 246, 0.18),
                0 0 40px rgba(59, 130, 246, 0.10),
                0 0 60px rgba(59, 130, 246, 0.07),
                inset 0 0 20px rgba(59, 130, 246, 0.04);
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease-out, neonGlow 2s ease-in-out infinite;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes neonGlow {
            0%, 100% {
                box-shadow: 
                    0 0 20px rgba(59, 130, 246, 0.18),
                    0 0 40px rgba(59, 130, 246, 0.10),
                    0 0 60px rgba(59, 130, 246, 0.07),
                    inset 0 0 20px rgba(59, 130, 246, 0.04);
            }
            50% {
                box-shadow: 
                    0 0 30px rgba(59, 130, 246, 0.25),
                    0 0 60px rgba(59, 130, 246, 0.13),
                    0 0 90px rgba(59, 130, 246, 0.09),
                    inset 0 0 30px rgba(59, 130, 246, 0.07);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 
                0 0 20px rgba(59, 130, 246, 0.18),
                0 0 40px rgba(59, 130, 246, 0.10);
            animation: logoGlow 2s ease-in-out infinite;
        }

        @keyframes logoGlow {
            0%, 100% {
                box-shadow: 
                    0 0 20px rgba(59, 130, 246, 0.18),
                    0 0 40px rgba(59, 130, 246, 0.10);
            }
            50% {
                box-shadow: 
                    0 0 30px rgba(59, 130, 246, 0.25),
                    0 0 60px rgba(59, 130, 246, 0.13);
            }
        }

        .logo-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
            filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.18));
        }

        .logo-section h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.18);
        }

        .logo-section p {
            font-size: 0.95rem;
            color: #60a5fa;
            font-weight: 400;
            text-shadow: 0 0 5px rgba(59, 130, 246, 0.10);
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #2563eb;
            transition: color 0.2s;
            text-shadow: 0 0 5px rgba(59, 130, 246, 0.10);
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #3b82f6;
            transition: all 0.2s;
            filter: drop-shadow(0 0 3px rgba(59, 130, 246, 0.10));
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            font-size: 0.95rem;
            border: 2px solid #3b82f6;
            border-radius: 12px;
            background: rgba(230, 242, 255, 0.7);
            color: #2563eb;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            background: rgba(230, 242, 255, 0.95);
            box-shadow: 
                0 0 10px rgba(59, 130, 246, 0.10),
                0 0 20px rgba(59, 130, 246, 0.07),
                inset 0 0 10px rgba(59, 130, 246, 0.04);
        }

        .form-group input:focus ~ .input-icon {
            color: #2563eb;
            filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.18));
        }

        .form-group input::placeholder {
            color: #666666;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
            border: 2px solid #3b82f6;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 
                0 0 20px rgba(59, 130, 246, 0.18),
                0 0 40px rgba(59, 130, 246, 0.10);
            position: relative;
            overflow: hidden;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 0 30px rgba(59, 130, 246, 0.25),
                0 0 60px rgba(59, 130, 246, 0.13);
            background: linear-gradient(135deg, #93c5fd 0%, #2563eb 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 0.875rem;
            background: rgba(59, 130, 246, 0.07);
            border: 1px solid #3b82f6;
            border-radius: 10px;
            color: #2563eb;
            font-size: 0.875rem;
            animation: shake 0.4s ease;
            box-shadow: 
                0 0 10px rgba(59, 130, 246, 0.10),
                inset 0 0 10px rgba(59, 130, 246, 0.04);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #2563eb;
            text-shadow: 0 0 3px rgba(59, 130, 246, 0.10);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #666666;
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #333333;
        }

        .divider span {
            padding: 0 1rem;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }

            .logo-section h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-4.41 0-8-3.59-8-8V8.41l8-4 8 4V12c0 4.41-3.59 8-8 8zm-1-6h2v2h-2v-2zm0-8h2v6h-2V6z"/>
                </svg>
            </div>
            <h1>Gestión Toliboy</h1>
            <p>Acceso a Documentación API</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <div class="input-wrapper">
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        placeholder="tu@email.com" 
                        required
                        autocomplete="email"
                    >
                    <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••" 
                        required
                        autocomplete="current-password"
                    >
                    <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Acceder a la Documentación
            </button>

            @error('email')
                <div class="error-message">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
            
            @error('password')
                <div class="error-message">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
        </form>

        <div class="footer-text">
            © 2025 Gestión Toliboy. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>