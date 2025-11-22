<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Presupuestos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- NAVBAR HORIZONTAL -->
    <nav class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 shadow-xl border-b-4 border-blue-700">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <!-- Logo y Título -->
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-yellow-400">⚓</span>
                    <span class="ml-3 text-xl font-bold text-white">Ankhor</span>
                </div>

                <!-- Menú de Navegación -->
                <div class="flex items-center space-x-4">
                    @if(Auth::user()->esProveedor())
                        {{-- MENÚ PROVEEDOR --}}
                        <a href="{{ route('proveedor.dashboard') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('proveedor.dashboard') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Inicio
                        </a>
                        <a href="{{ route('proveedor.solicitudes') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('proveedor.solicitudes') || request()->routeIs('proveedor.solicitud.*') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Solicitudes
                        </a>
                        <a href="{{ route('proveedor.perfil') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('proveedor.perfil') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Mi Perfil
                        </a>
                    @else
                        {{-- MENÚ ANKOR (Colaboradores/Admin) --}}
                        <a href="{{ route('pedidos-cliente.index') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('pedidos-cliente.*') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Pedidos
                        </a>
                        <a href="{{ route('solicitudes-presupuesto.index') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('solicitudes-presupuesto.*') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Cotizaciones
                        </a>
                        <a href="{{ route('ordenes-compra.index') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('ordenes-compra.*') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Compras
                        </a>
                        <a href="{{ route('ordenes-envio.index') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('ordenes-envio.*') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Envíos
                        </a>
                        <span class="text-blue-600">|</span>
                        <a href="{{ route('proveedores.index') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('proveedores.*') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Proveedores
                        </a>
                        <a href="{{ route('inventario.index') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('inventario.*') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Inventario
                        </a>
                        <a href="{{ route('productos.index') }}"
                           class="text-blue-100 hover:text-white px-3 py-2 text-sm font-medium transition
                                  {{ request()->routeIs('productos.*') ? 'text-white bg-blue-700 rounded-lg' : '' }}">
                            Productos
                        </a>
                        <a href="{{ route('pedidos-cliente.create') }}"
                           class="bg-yellow-500 text-blue-900 px-4 py-2 rounded-lg text-sm font-bold hover:bg-yellow-400 transition shadow-md">
                            + Nuevo
                        </a>
                    @endif

                    <!-- User Menu -->
                    <div class="flex items-center space-x-3 ml-4 pl-4 border-l border-blue-600">
                        <span class="text-blue-100 text-sm">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-blue-300">({{ Auth::user()->rol_nombre }})</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-300 hover:text-red-100 text-sm font-medium transition">
                                Salir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Mensajes Flash -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
