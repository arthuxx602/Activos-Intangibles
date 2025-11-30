<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panel')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
      body { background:#f6f8fc; }
      .layout-shell{ display:flex; min-height:100vh; }
      .sidebar{ width:260px; background:#0f172a; color:#e2e8f0; padding:18px 14px; position:sticky; top:0; align-self:flex-start; min-height:100vh; }
      .sidebar .brand{ font-weight:700; font-size:1.2rem; letter-spacing:0.5px; margin-bottom:20px; }
      .sidebar a{ color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:10px; font-weight:600; }
      .sidebar a:hover, .sidebar a.active{ background:#1e293b; color:#fff; }
      .sidebar .accordion-button{ background:transparent; color:inherit; padding:10px 12px; border-radius:10px; border:none; box-shadow:none; gap:10px; font-weight:700; }
      .sidebar .accordion-button:not(.collapsed){ background:#1e293b; color:#fff; }
      .sidebar .accordion-body{ padding:0 0 10px 12px; }
      .sidebar .accordion-body a{ padding:8px 12px 8px 32px; font-weight:500; }
      .sidebar .nav-section-title{ text-transform:uppercase; color:#94a3b8; font-size:0.75rem; padding:12px 12px 6px; letter-spacing:1px; }
      main{ flex:1; padding:24px; }
      header.page-header{ display:flex; align-items:center; gap:12px; margin-bottom:20px; }
      header.page-header h1{ margin:0; font-size:1.5rem; font-weight:700; color:#0f172a; }
    </style>
</head>
<body>
  <div class="layout-shell">
    <aside class="sidebar">
      <div class="brand d-flex align-items-center gap-2">
        <i class="bi bi-bezier2"></i>
        <span>Sipmainputvalue</span>
      </div>

      <nav class="nav flex-column">
        <div class="nav-section-title">Menú</div>
        <a href="{{ route('admin.inicio') }}" class="{{ request()->routeIs('admin.inicio') ? 'active' : '' }}">
          <i class="bi bi-house-door"></i> <span>Inicio</span>
        </a>

        <div class="accordion" id="sidebarAccordion">
          <div class="accordion-item" style="background:transparent;border:none;">
            <h2 class="accordion-header">
              <button class="accordion-button {{ request()->routeIs('admin.empresas','admin.ubicacion','admin.usuarios','admin.tipos-inversion','admin.inversiones','admin.tasas') ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#parametrosMenu" aria-expanded="{{ request()->routeIs('admin.empresas','admin.ubicacion','admin.usuarios','admin.tipos-inversion','admin.inversiones','admin.tasas') ? 'true' : 'false' }}" aria-controls="parametrosMenu">
                <i class="bi bi-gear"></i> <span>Parámetros</span>
              </button>
            </h2>
            <div id="parametrosMenu" class="accordion-collapse collapse {{ request()->routeIs('admin.empresas','admin.ubicacion','admin.usuarios','admin.tipos-inversion','admin.inversiones','admin.tasas') ? 'show' : '' }}" data-bs-parent="#sidebarAccordion">
              <div class="accordion-body">
                <a href="{{ route('admin.empresas') }}" class="{{ request()->routeIs('admin.empresas') ? 'active' : '' }}">
                  <i class="bi bi-building"></i> <span>Empresas</span>
                </a>
                <a href="{{ route('admin.ubicacion') }}" class="{{ request()->routeIs('admin.ubicacion') ? 'active' : '' }}">
                  <i class="bi bi-geo-alt"></i> <span>Ubicación</span>
                </a>
                <a href="{{ route('admin.usuarios') }}" class="{{ request()->routeIs('admin.usuarios') ? 'active' : '' }}">
                  <i class="bi bi-people"></i> <span>Usuarios</span>
                </a>
                <a href="{{ route('admin.tipos-inversion') }}" class="{{ request()->routeIs('admin.tipos-inversion') ? 'active' : '' }}">
                  <i class="bi bi-diagram-3"></i> <span>Tipos de Inversiones</span>
                </a>
                <a href="{{ route('admin.inversiones') }}" class="{{ request()->routeIs('admin.inversiones') ? 'active' : '' }}">
                  <i class="bi bi-cash-coin"></i> <span>Inversiones</span>
                </a>
                <a href="{{ route('admin.tasas') }}" class="{{ request()->routeIs('admin.tasas') ? 'active' : '' }}">
                  <i class="bi bi-percent"></i> <span>Tasas de interés</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <a href="{{ route('admin.simulacion') }}" class="{{ request()->routeIs('admin.simulacion') ? 'active' : '' }}">
          <i class="bi bi-diagram-3"></i> <span>Simulación</span>
        </a>
        <a href="{{ route('admin.liquidacion') }}" class="{{ request()->routeIs('admin.liquidacion') ? 'active' : '' }}">
          <i class="bi bi-scales"></i> <span>Liquidación</span>
        </a>
        <a href="{{ route('admin.consultas') }}" class="{{ request()->routeIs('admin.consultas') ? 'active' : '' }}">
          <i class="bi bi-table"></i> <span>Consultas</span>
        </a>
      </nav>
    </aside>

    <main>
      <header class="page-header">
        <h1>@yield('page-title', 'Panel')</h1>
      </header>
      @yield('content')
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
