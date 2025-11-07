<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'ERP Distribuidora')</title>
<link rel="icon" href="https://img.icons8.com/ios-filled/50/2563EB/truck.png" type="image/png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
.sidebar{background-color:#1D4ED8;min-height:100vh;display:flex;flex-direction:column;justify-content:space-between}
.sidebar a{color:#fff;font-weight:500;display:flex;align-items:center;padding:12px 20px;border-radius:.5rem;transition:all .3s ease;font-style:italic}
.sidebar a:hover{background-color:#2563EB;text-decoration:none;transform:translateX(5px)}
.sidebar .active{background-color:#2563EB;box-shadow:0 0 10px rgba(255,255,255,.3)}
.sidebar i{margin-right:12px}
.sidebar .sidebar-header{text-align:center;margin-bottom:1rem;padding:1rem;border-radius:1rem;background:linear-gradient(to right,#1E40AF,#2563EB);box-shadow:0 4px 10px rgba(0,0,0,.2)}
.sidebar .sidebar-header img{width:50px;height:50px;background:#fff;padding:6px;border-radius:50%;transition:transform .3s}
.sidebar .sidebar-header img:hover{transform:rotate(15deg)}
.sidebar .sidebar-footer{text-align:center;font-size:.85rem;color:rgba(255,255,255,.6);padding:10px 0;font-style:italic}
.header-top{background-color:#fff;padding:12px 20px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 12px rgba(0,0,0,.1);flex-wrap:wrap;border-bottom:2px solid #3b82f6;font-style:italic}
.header-top .logo-title{display:flex;align-items:center;gap:8px}
.header-top .logo-title img{width:45px;height:45px;transition:transform .3s ease}
.header-top .logo-title img:hover{transform:rotate(20deg)}
</style>
@stack('styles')
</head>
<body class="bg-gray-100">
<div class="d-flex flex-column flex-lg-row">
  <aside class="sidebar p-4 flex-shrink-0" style="width:260px">
    <div>
      <div class="sidebar-header mb-4">
        <div class="d-flex justify-content-center align-items-center mb-2">
          <img src="https://img.icons8.com/ios-filled/50/2563EB/truck.png" alt="Logo">
          <h3 class="text-white fw-bold mb-0 ms-2">ERP Distribuidora</h3>
        </div>
        <small class="text-white-50 fw-light">Sistema de Gestión</small>
      </div>
      <hr>
      <ul class="nav flex-column gap-2">
        <li><a href="{{ route('dashboard') }}" class="nav-link @if(request()->routeIs('dashboard')) active @endif"><i class="fas fa-home"></i>Dashboard</a></li>
        <li><a href="{{ route('productos.index') }}" class="nav-link @if(request()->routeIs('productos.*')) active @endif"><i class="fas fa-box-open"></i>Productos</a></li>
        <li><a href="{{ route('clientes.index') }}" class="nav-link @if(request()->routeIs('clientes.*')) active @endif"><i class="fas fa-users"></i>Clientes</a></li>
        <li><a href="{{ route('proveedores.index') }}" class="nav-link @if(request()->routeIs('proveedores.*')) active @endif"><i class="fas fa-truck"></i>Proveedores</a></li>
        <li><a href="{{ route('presupuestos.index') }}" class="nav-link @if(request()->routeIs('presupuestos.*')) active @endif"><i class="fas fa-file-invoice"></i>Presupuestos</a></li>
        <li><a href="{{ route('ventas.index') }}" class="nav-link @if(request()->routeIs('ventas.*')) active @endif"><i class="fas fa-shopping-cart"></i>Ventas</a></li>
        <li><a href="{{ route('compras.index') }}" class="nav-link @if(request()->routeIs('compras.*')) active @endif"><i class="fas fa-shopping-bag"></i>Compras</a></li>
        <li><a href="{{ route('inventario.index') }}" class="nav-link @if(request()->routeIs('inventario.*')) active @endif"><i class="fas fa-warehouse"></i>Inventario</a></li>
      </ul>
    </div>
    <div class="sidebar-footer">© 2025 ERP DISTRIBUIDORA</div>
  </aside>
  <div class="flex-fill">
    <header class="header-top">
      <div class="logo-title">
        <img src="https://img.icons8.com/ios-filled/50/2563EB/delivery.png" alt="Logo">
        <h5 class="mb-0 fw-bold">DISTRIBUCIÓN Y LOGÍSTICA</h5>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="text-gray-600 fw-semibold"><i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, DD [de] MMMM [de] YYYY') }}</span>
        <div class="dropdown">
          <a href="#" class="text-dark text-decoration-none dropdown-toggle d-flex align-items-center" id="userMenu" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle fa-2x me-1"></i> {{ session('usuario')->usu_nombre ?? 'Usuario' }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
            <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>
          </ul>
        </div>
      </div>
    </header>
    <main class="p-4" style="min-height:calc(100vh - 70px)">
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif
      @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif
      @yield('content')
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
