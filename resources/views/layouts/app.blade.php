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
.sidebar{
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
    min-height:100vh;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    box-shadow: 4px 0 20px rgba(0,0,0,0.3);
}
.sidebar a{
    color:#e2e8f0;
    font-weight:600;
    display:flex;
    align-items:center;
    padding:14px 20px;
    border-radius:.5rem;
    transition:all .3s ease;
    margin-bottom: 4px;
}
.sidebar a:hover{
    background-color:rgba(37, 99, 235, 0.15);
    color:#fff;
    text-decoration:none;
    transform:translateX(5px);
    border-left: 3px solid #2563eb;
}
.sidebar .active{
    background: linear-gradient(90deg, rgba(37, 99, 235, 0.2) 0%, rgba(37, 99, 235, 0.05) 100%);
    color:#fff;
    border-left: 3px solid #2563eb;
    box-shadow:0 4px 12px rgba(37, 99, 235, 0.2);
}
.sidebar i{margin-right:12px; font-size: 1.1rem;}
.sidebar .sidebar-header{
    text-align:center;
    margin-bottom:1.5rem;
    padding:1.5rem 1rem;
    border-radius:1rem;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow:0 8px 20px rgba(37, 99, 235, 0.3);
}
.sidebar .sidebar-header img{
    width:55px;
    height:55px;
    background:#fff;
    padding:8px;
    border-radius:50%;
    transition:transform .3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.sidebar .sidebar-header img:hover{transform:rotate(15deg) scale(1.05);}
.sidebar .sidebar-footer{
    text-align:center;
    font-size:.8rem;
    color:#64748b;
    padding:12px 0;
    border-top: 1px solid rgba(255,255,255,0.1);
}
.header-top{
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
    padding:14px 24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
    flex-wrap:wrap;
    border-bottom:3px solid #2563eb;
}
.header-top .logo-title{display:flex;align-items:center;gap:10px;}
.header-top .logo-title img{
    width:48px;
    height:48px;
    transition:transform .3s ease;
    filter: drop-shadow(0 2px 4px rgba(255, 255, 255, 0.3));
}
.header-top .logo-title img:hover{transform:rotate(360deg) scale(1.1);}
.header-top .logo-title h5{
    color: #ffffff;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.card{
    background: rgba(30, 41, 59, 0.04) !important;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
.card-body{
    background: rgba(30, 41, 59, 0.04);
}
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
        <span class="text-white fw-semibold"><i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, DD [de] MMMM [de] YYYY') }}</span>
        <div class="dropdown">
          <a href="#" class="text-white text-decoration-none dropdown-toggle d-flex align-items-center" id="userMenu" data-bs-toggle="dropdown">
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
