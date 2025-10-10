<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Panel')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body { background:#f8fafc; }
    .sidebar { width: 260px; background: #111827; color:#fff; position:fixed; top:0; bottom:0; }
    .sidebar a { color:#d1d5db; text-decoration:none; display:block; padding:.65rem 1rem; border-radius:.5rem; }
    .sidebar a.active, .sidebar a:hover { background:#1f2937; color:#fff; }
    .sidebar .brand { font-weight:700; padding:1rem; border-bottom:1px solid #1f2937; }
    .content { margin-left:260px; min-height:100vh; display:flex; flex-direction:column; }
    .topbar { background:#fff; border-bottom:1px solid #e5e7eb; }
    .user-badge { width:36px; height:36px; border-radius:50%; background:#0ea5e9; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; }
    .container-page { flex:1; padding:1rem 1.25rem 2rem; }
  </style>

  @yield('head')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar d-none d-md-block">
  <div class="brand d-flex align-items-center gap-2">
    <i class="bi bi-grid-1x2"></i> SIPMAINPUTVALUE
  </div>
  <nav class="p-2">
    <div class="text-uppercase text-secondary small px-2 mt-2 mb-1">Menú</div>
    <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'active' : '' }}">
      <i class="bi bi-house-door me-2"></i> Inicio
    </a>

    <div class="text-uppercase text-secondary small px-2 mt-3 mb-1">Parámetros</div>
    <a href="{{ route('empresas.index') }}"  class="{{ request()->routeIs('empresas.*') ? 'active' : '' }}">
      <i class="bi bi-building me-2"></i> Empresas
    </a>
    <a href="{{ route('usuarios.index') }}"  class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
      <i class="bi bi-people me-2"></i> Usuarios
    </a>
    <a href="{{ route('ubicacion.index') }}" class="{{ request()->routeIs('ubicacion.*') ? 'active' : '' }}">
      <i class="bi bi-geo-alt me-2"></i> Ubicación
    </a>
    <a href="{{ route('inversiones.index') }}" class="{{ request()->routeIs('inversiones.*') ? 'active' : '' }}">
      <i class="bi bi-cash-coin me-2"></i> Inversiones
    </a>
    <a href="{{ route('tasas.index') }}" class="{{ request()->routeIs('tasas.*') ? 'active' : '' }}">
      <i class="bi bi-percent me-2"></i> Tasas de interés
    </a>

    <div class="text-uppercase text-secondary small px-2 mt-3 mb-1">Cálculo</div>
    <a href="{{ route('simulacion.index') }}" class="{{ request()->routeIs('simulacion.*') ? 'active' : '' }}">
      <i class="bi bi-pie-chart me-2"></i> Simulación
    </a>
    <a href="{{ route('liquidacion.index') }}" class="{{ request()->routeIs('liquidacion.*') ? 'active' : '' }}">
      <i class="bi bi-balance-scale me-2"></i> Distribuir participación
    </a>

    <div class="text-uppercase text-secondary small px-2 mt-3 mb-1">Consultas</div>
    <a href="{{ route('consultas.index') }}" class="{{ request()->routeIs('consultas.*') ? 'active' : '' }}">
      <i class="bi bi-table me-2"></i> Consultas
    </a>
  </nav>
</aside>

<!-- TOPBAR + CONTENT -->
<div class="content">
  <header class="topbar py-2 px-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-outline-secondary btn-sm d-md-none" type="button" onclick="document.querySelector('.sidebar').classList.toggle('d-none')">
        <i class="bi bi-list"></i>
      </button>
      <div class="fw-semibold">@yield('page-title', 'Panel')</div>
    </div>
    <div class="d-flex align-items-center gap-2">
      @php
        $nombre = session('nombre', 'Usuario');
        $apellido = session('apellido', '');
        $letra = mb_strtoupper(mb_substr($nombre, 0, 1));
      @endphp
      <div class="user-badge">{{ $letra }}</div>
      <div class="small">{{ $nombre.' '.$apellido }}</div>
    </div>
  </header>

  <main class="container-page">
    @yield('content')
  </main>

  <footer class="px-3 py-2 small text-secondary">
    © {{ date('Y') }} SIPMAINPUTVALUE
  </footer>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
