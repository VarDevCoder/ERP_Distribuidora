<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - ERP Distribuidora</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
    body {
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
    }
    .login-card {
        background: #ffffff;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }
    .btn-primary {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(220, 38, 38, 0.4);
    }
    .input-field:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    .logo-container {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        border-radius: 50%;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(220, 38, 38, 0.3);
    }
</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

<div class="login-card rounded-2xl p-10 w-full max-w-md">
    <!-- Logo -->
    <div class="flex justify-center mb-6">
        <div class="logo-container">
            <img src="https://img.icons8.com/ios-filled/80/ffffff/truck.png" class="w-16 h-16">
        </div>
    </div>

    <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">ERP Distribuidora</h2>
    <p class="text-center text-gray-500 text-sm mb-8">Sistema de Gestión Empresarial</p>

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-600 text-red-800 px-4 py-3 rounded mb-6 flex items-center">
            <i class="fas fa-exclamation-triangle mr-3 text-red-600"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
        @csrf
        <div class="relative">
            <i class="fas fa-envelope absolute left-4 top-4 text-gray-400"></i>
            <input type="email" name="usu_email" placeholder="Correo electrónico" required
                   class="input-field w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 rounded-lg focus:outline-none transition duration-300 text-gray-700">
        </div>

        <div class="relative">
            <i class="fas fa-lock absolute left-4 top-4 text-gray-400"></i>
            <input type="password" name="usu_pass" placeholder="Contraseña" required
                   class="input-field w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 rounded-lg focus:outline-none transition duration-300 text-gray-700">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center text-gray-600 cursor-pointer">
                <input type="checkbox" class="mr-2 w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                Recordarme
            </label>
            <a href="#" class="text-red-600 hover:text-red-700 font-medium">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn-primary w-full text-white font-bold py-3.5 rounded-lg shadow-lg">
            <i class="fas fa-sign-in-alt mr-2"></i>Ingresar al Sistema
        </button>
    </form>

    <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-center text-gray-400 text-xs">
            © 2025 ERP Distribuidora. Todos los derechos reservados.
        </p>
    </div>
</div>

</body>
</html>
