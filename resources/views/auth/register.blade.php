<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Ankhor ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Animated background */
        .animated-bg {
            background: linear-gradient(-45deg, #059669, #10b981, #047857, #14b8a6);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* Floating shapes */
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 20%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            left: 80%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 10%;
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 30%;
            left: 70%;
            animation-delay: 6s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            25% {
                transform: translateY(-30px) translateX(30px);
            }
            50% {
                transform: translateY(-60px) translateX(-30px);
            }
            75% {
                transform: translateY(-30px) translateX(60px);
            }
        }
    </style>
</head>
<body class="h-screen overflow-hidden">
    <div class="flex h-full">
        <!-- Left Side - Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 overflow-y-auto">
            <div class="w-full max-w-md my-8">
                <!-- Logo and Brand -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <span class="text-6xl">⚓</span>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Ankhor</h1>
                    <p class="text-gray-600">Sistema de Gestión Empresarial</p>
                </div>

                <!-- Register Form -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Crear Cuenta</h2>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border-2 border-red-200 text-red-700 px-4 py-3 rounded-lg">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.post') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre Completo
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                                placeholder="Ingresa tu nombre completo"
                                required
                                autofocus
                            >
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                                placeholder="Ingresa tu email"
                                required
                            >
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña
                            </label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                                placeholder="Mínimo 8 caracteres"
                                required
                            >
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Contraseña
                            </label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                                placeholder="Repite tu contraseña"
                                required
                            >
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition duration-200 shadow-lg"
                        >
                            Crear Cuenta
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            ¿Ya tienes una cuenta?
                            <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 font-semibold">
                                Inicia sesión aquí
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Animated Background -->
        <div class="hidden lg:flex lg:w-1/2 animated-bg relative items-center justify-center overflow-hidden">
            <!-- Floating Shapes -->
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>

            <!-- Content -->
            <div class="relative z-10 text-center text-white px-12">
                <h2 class="text-5xl font-bold mb-6">Únete a Ankhor</h2>
                <p class="text-xl text-green-100 mb-8">
                    Comienza a gestionar tu negocio de forma profesional y eficiente
                </p>
                <div class="grid grid-cols-1 gap-6 mt-12 max-w-md mx-auto">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20 text-left">
                        <div class="flex items-center mb-3">
                            <span class="text-3xl mr-3">✅</span>
                            <h3 class="font-semibold text-lg">Registro Fácil</h3>
                        </div>
                        <p class="text-sm text-green-100">Crea tu cuenta en menos de un minuto</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20 text-left">
                        <div class="flex items-center mb-3">
                            <span class="text-3xl mr-3">🔒</span>
                            <h3 class="font-semibold text-lg">100% Seguro</h3>
                        </div>
                        <p class="text-sm text-green-100">Tus datos están protegidos y encriptados</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20 text-left">
                        <div class="flex items-center mb-3">
                            <span class="text-3xl mr-3">🚀</span>
                            <h3 class="font-semibold text-lg">Acceso Inmediato</h3>
                        </div>
                        <p class="text-sm text-green-100">Empieza a usar el sistema de inmediato</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20 text-left">
                        <div class="flex items-center mb-3">
                            <span class="text-3xl mr-3">💡</span>
                            <h3 class="font-semibold text-lg">Soporte 24/7</h3>
                        </div>
                        <p class="text-sm text-green-100">Estamos aquí para ayudarte en todo momento</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
