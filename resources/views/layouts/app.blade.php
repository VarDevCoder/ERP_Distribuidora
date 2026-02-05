<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ankhor Distribuidora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex flex-col" x-data="{ mobileMenu: false }">

    <!-- NAVBAR -->
    <nav class="nav-bar sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-14">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 group">
                    <span class="text-2xl group-hover:scale-110 transition-transform duration-200">⚓</span>
                    <span class="text-lg font-bold text-white tracking-tight">Ankhor</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden lg:flex items-center gap-1">
                    @if(Auth::user()->esProveedor())
                        <x-nav-link :href="route('proveedor.dashboard')" :active="request()->routeIs('proveedor.dashboard')">Inicio</x-nav-link>
                        <x-nav-link :href="route('proveedor.solicitudes')" :active="request()->routeIs('proveedor.solicitudes') || request()->routeIs('proveedor.solicitud.*')">Solicitudes</x-nav-link>
                        <x-nav-link :href="route('proveedor-productos.index')" :active="request()->routeIs('proveedor-productos.*')">Mi Catálogo</x-nav-link>
                        <x-nav-link :href="route('proveedor.perfil')" :active="request()->routeIs('proveedor.perfil')">Mi Perfil</x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Inicio</x-nav-link>
                        <x-nav-link :href="route('pedidos-cliente.index')" :active="request()->routeIs('pedidos-cliente.*')">Solicitudes</x-nav-link>
                        <x-nav-link :href="route('solicitudes-presupuesto.index')" :active="request()->routeIs('solicitudes-presupuesto.*')">Cotizaciones</x-nav-link>
                        <x-nav-link :href="route('ordenes-compra.index')" :active="request()->routeIs('ordenes-compra.*')">Compras</x-nav-link>
                        <x-nav-link :href="route('ordenes-envio.index')" :active="request()->routeIs('ordenes-envio.*')">Envíos</x-nav-link>
                        <span class="w-px h-6 bg-blue-600"></span>
                        <x-nav-link :href="route('proveedores.index')" :active="request()->routeIs('proveedores.*')">Proveedores</x-nav-link>
                        <x-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">Clientes</x-nav-link>
                        <x-nav-link :href="route('analisis-proveedores.index')" :active="request()->routeIs('analisis-proveedores.*')">Análisis</x-nav-link>
                        <x-nav-link :href="route('inventario.index')" :active="request()->routeIs('inventario.*')">Inventario</x-nav-link>
                        <x-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')">Productos</x-nav-link>
                        <x-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">Categorías</x-nav-link>
                    @endif
                </div>

                <!-- User + Hamburger -->
                <div class="flex items-center gap-3">
                    <!-- User badge (always visible) -->
                    <div class="hidden sm:flex items-center gap-2 text-sm">
                        <div class="w-8 h-8 rounded-full bg-blue-600 border-2 border-blue-400 flex items-center justify-center text-white font-bold text-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:block">
                            <span class="text-white font-medium">{{ Auth::user()->name }}</span>
                            <span class="text-blue-300 text-xs block leading-none">{{ Auth::user()->rol_nombre }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="text-red-300 hover:text-white hover:bg-red-500/30 px-2 py-1 rounded text-xs font-medium transition-all duration-200">
                            Salir
                        </button>
                    </form>

                    <!-- Hamburger (mobile) -->
                    <button @click="mobileMenu = !mobileMenu" class="lg:hidden text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenu"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             @click.outside="mobileMenu = false"
             class="lg:hidden border-t border-blue-700 bg-blue-900/95 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 py-3 space-y-1">
                @if(Auth::user()->esProveedor())
                    <x-mobile-nav-link :href="route('proveedor.dashboard')" :active="request()->routeIs('proveedor.dashboard')">Inicio</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('proveedor.solicitudes')" :active="request()->routeIs('proveedor.solicitudes') || request()->routeIs('proveedor.solicitud.*')">Solicitudes</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('proveedor-productos.index')" :active="request()->routeIs('proveedor-productos.*')">Mi Catálogo</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('proveedor.perfil')" :active="request()->routeIs('proveedor.perfil')">Mi Perfil</x-mobile-nav-link>
                @else
                    <x-mobile-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Inicio</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('pedidos-cliente.index')" :active="request()->routeIs('pedidos-cliente.*')">Solicitudes</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('solicitudes-presupuesto.index')" :active="request()->routeIs('solicitudes-presupuesto.*')">Cotizaciones</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('ordenes-compra.index')" :active="request()->routeIs('ordenes-compra.*')">Compras</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('ordenes-envio.index')" :active="request()->routeIs('ordenes-envio.*')">Envíos</x-mobile-nav-link>
                    <div class="border-t border-blue-700 my-2"></div>
                    <x-mobile-nav-link :href="route('proveedores.index')" :active="request()->routeIs('proveedores.*')">Proveedores</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">Clientes</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('analisis-proveedores.index')" :active="request()->routeIs('analisis-proveedores.*')">Análisis Proveedores</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('inventario.index')" :active="request()->routeIs('inventario.*')">Inventario</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')">Productos</x-mobile-nav-link>
                    <x-mobile-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">Categorías</x-mobile-nav-link>
                @endif

                <!-- Mobile user info -->
                <div class="border-t border-blue-700 pt-3 mt-2 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-blue-600 border-2 border-blue-400 flex items-center justify-center text-white font-bold text-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-white text-sm font-medium">{{ Auth::user()->name }}</span>
                            <span class="text-blue-300 text-xs block">{{ Auth::user()->rol_nombre }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-300 hover:text-white bg-red-500/20 hover:bg-red-500/40 px-3 py-1.5 rounded text-sm font-medium transition-all">
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 py-6 flex-1">
        @yield('content')
    </main>

    <!-- Footer minimalista -->
    <footer class="text-center text-xs text-gray-400 py-4 border-t border-gray-200">
        Ankhor Distribuidora &copy; {{ date('Y') }}
    </footer>

    <!-- Toast de mensajes flash via SweetAlert2 -->
    @if(session('success') || session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                showClass: { popup: 'animate__animated animate__slideInRight' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp' }
            });
            @endif
            @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: @json(session('error')),
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });
            @endif
        });
    </script>
    @endif
</body>
</html>
