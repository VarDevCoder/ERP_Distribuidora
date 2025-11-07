<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - ERP Distribuidora</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body class="bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center min-h-screen">

<div class="bg-white shadow-2xl rounded-3xl p-10 w-full max-w-md relative overflow-hidden">
    <!-- Logo -->
    <div class="absolute top-0 left-0 right-0 mt-6 flex justify-center">
        <img src="https://img.icons8.com/ios-filled/100/000000/truck.png" class="w-20 h-20">
    </div>

    <h2 class="text-3xl font-bold text-center text-gray-700 mt-24 mb-8">ERP Distribuidora</h2>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
        @csrf
        <div class="relative">
            <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
            <input type="email" name="usu_email" placeholder="Correo electrónico" required
                   class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
        </div>

        <div class="relative">
            <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
            <input type="password" name="usu_pass" placeholder="Contraseña" required
                   class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center">
                <input type="checkbox" class="mr-2">
                Recordarme
            </label>
            <a href="#" class="text-blue-600 hover:underline">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition duration-300 transform hover:scale-105">
            Ingresar
        </button>
    </form>

    <p class="text-center text-gray-500 text-sm mt-6">© 2025 ERP Distribuidora. Todos los derechos reservados.</p>
</div>

</body>
</html>
