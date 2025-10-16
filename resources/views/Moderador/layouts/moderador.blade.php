<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Panel Moderador')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    .user-icon{
      display:inline-block;width:40px;height:40px;border-radius:50%;
      background:#28a745;color:#fff;text-align:center;line-height:40px;
      font-size:18px;font-weight:700;
    }
    .left-side-bar{width:260px;background:#0f172a;color:#cbd5e1;position:fixed;top:0;bottom:0}
    .left-side-bar a{color:#cbd5e1;text-decoration:none}
    .left-side-bar .brand-logo{padding:16px;display:flex;align-items:center;justify-content:space-between}
    .menu-block{padding:8px 0;overflow:auto;height:calc(100vh - 56px)}
    .sidebar-menu ul{list-style:none;margin:0;padding:0}
    .sidebar-menu li a{display:flex;gap:8px;align-items:center;padding:10px 16px;border-radius:10px;margin:2px 8px}
    .sidebar-menu li a.active, .sidebar-menu li a:hover{background:#1e293b}
    .header{position:fixed;left:260px;right:0;top:0;height:56px;background:#fff;
      border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;padding:0 16px;z-index:50}
    .header .header-left{display:flex;align-items:center;gap:12px}
    .header .menu-icon{font-size:20px;cursor:pointer}
    .header .header-search h2{margin:0;font-size:18px}
    .header-right{display:flex;align-items:center;gap:12px}
    .main-container{padding-top:72px;padding-left:276px;padding-right:16px;padding-bottom:24px}
    .right-sidebar{position:fixed;right:-360px;top:0;bottom:0;width:360px;background:#fff;border-left:1px solid #e5e7eb;transition:right .2s ease;z-index:60}
    .right-sidebar.open{right:0}
    .right-sidebar .sidebar-title{display:flex;align-items:center;justify-content:space-between;padding:16px;border-bottom:1px solid #e5e7eb}
    .mobile-menu-overlay{display:none}
    @media (max-width: 992px){
      .left-side-bar{left:-260px;transition:left .2s ease}
      .left-side-bar.open{left:0}
      .header{left:0}
      .main-container{padding-left:16px}
      .mobile-menu-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:40}
      .mobile-menu-overlay.show{display:block}
    }
    .micon{width:20px;display:inline-flex;justify-content:center}
    .dropdown-menu{min-width:220px}
  </style>
</head>
@php
  // Traemos nombre/apellido desde sesión (migración gradual)
  $nombre   = session('nombre', '');
  $apellido = session('apellido', '');
  $letra    = mb_strtoupper(mb_substr($nombre, 0, 1));
@endphp
<body>

  {{-- Header --}}
  <div class="header">
    <div class="header-left">
      <div class="menu-icon bi bi-list" id="btn-open-sidebar"></div>
      <div class="header-search">
        <h2>Moderador</h2>
      </div>
    </div>

    <div class="header-right">
      <div class="dashboard-setting user-notification">
        <a class="no-arrow" href="javascript:;" id="btn-open-right">
          <i class="icon-copy fa fa-adjust"></i>
        </a>
      </div>

      {{-- Dropdown usuario --}}
      <div class="dropdown">
        <a class="dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="user-icon">{{ $letra ?: 'U' }}</span>
          <span class="user-name">{{ trim($nombre.' '.$apellido) ?: 'Usuario' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          {{-- Logout Laravel (POST) --}}
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="dropdown-item" type="submit">
                <i class="dw dw-logout me-2"></i> Cerrar sesión
              </button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>

  {{-- Right Sidebar (Ajustes visuales) --}}
  <div class="right-sidebar" id="right-sidebar">
    <div class="sidebar-title">
      <h3 class="weight-600 font-16 text-blue mb-0">
        Ajustes Visuales
        <span class="d-block fw-normal fs-6">Interfaz Moderador</span>
      </h3>
      <button class="btn btn-sm btn-light" id="btn-close-right">
        <i class="icon-copy ion-close-round"></i>
      </button>
    </div>

    <div class="right-sidebar-body p-3">
      <div class="right-sidebar-body-content">
        <h4 class="fw-semibold fs-5 pb-2">Color - Encabezado</h4>
        <div class="d-flex gap-2 pb-3 mb-2">
          <button class="btn btn-outline-primary header-white active">Blanco</button>
          <button class="btn btn-outline-primary header-dark">Oscuro</button>
        </div>

        <h4 class="fw-semibold fs-5 pb-2">Color - barra de navegación</h4>
        <div class="d-flex gap-2 pb-3 mb-2">
          <button class="btn btn-outline-primary sidebar-light">Blanco</button>
          <button class="btn btn-outline-primary sidebar-dark active">Oscuro</button>
        </div>

        <h4 class="fw-semibold fs-5 pb-2">Ícono del menú</h4>
        <div class="d-flex gap-2 pb-2 mb-2">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="menu-dropdown-icon" id="sidebaricon-1" value="icon-style-1" checked>
            <label class="form-check-label" for="sidebaricon-1"><i class="fa fa-angle-down"></i></label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="menu-dropdown-icon" id="sidebaricon-2" value="icon-style-2">
            <label class="form-check-label" for="sidebaricon-2"><i class="ion-plus-round"></i></label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="menu-dropdown-icon" id="sidebaricon-3" value="icon-style-3">
            <label class="form-check-label" for="sidebaricon-3"><i class="fa fa-angle-double-right"></i></label>
          </div>
        </div>

        <h4 class="fw-semibold fs-5 pb-2">Ícono de la lista</h4>
        <div class="d-flex flex-wrap gap-2 pb-3 mb-2">
          @for($i=1;$i<=6;$i++)
            <div class="form-check">
              <input class="form-check-input" type="radio" name="menu-list-icon" id="sidebariconlist-{{ $i }}" value="icon-list-style-{{ $i }}" {{ in_array($i,[1,4]) ? 'checked' : '' }}>
              <label class="form-check-label" for="sidebariconlist-{{ $i }}">#{{ $i }}</label>
            </div>
          @endfor
        </div>

        <div class="pt-3 text-center">
          <button class="btn btn-danger" id="reset-settings">Restablecer ajustes</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Sidebar izquierda --}}
  <div class="left-side-bar" id="left-sidebar">
    <div class="brand-logo">
      <a href="{{ route('moderador.inicio', absolute:false) }}">
        {{-- Pon tu logo si aplica --}}
        <img src="" alt="" class="dark-logo">
      </a>
      <button class="btn btn-sm btn-light" id="btn-close-sidebar"><i class="ion-close-round"></i></button>
    </div>

    <div class="menu-block">
      <div class="sidebar-menu">
        <ul id="accordion-menu">
          <li>
            <a href="{{ route('moderador.inicio', absolute:false) }}"
               class="{{ request()->routeIs('moderador.inicio') ? 'active' : '' }}">
              <span class="micon fa fa-home"></span><span class="mtext">Inicio</span>
            </a>
          </li>

          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle">
              <span class="micon bi bi-gear"></span><span class="mtext">Parámetros</span>
            </a>
            <ul class="submenu" style="list-style:none;margin:6px 0 10px 36px;padding-left:0;">
              <li>
                <a href="{{ route('moderador.inversiones.index') }}"
                   class="{{ request()->routeIs('moderador.inversiones.*') ? 'active' : '' }}">
                   Inversiones
                </a>
              </li>
              <li>
                <a href="{{ route('moderador.usuarios.index') }}"
                   class="{{ request()->routeIs('moderador.usuarios.*') ? 'active' : '' }}">
                   Usuarios
                </a>
              </li>
            </ul>
          </li>

          <li>
            @php
              // si luego creas el módulo de consultas, define la ruta y cámbialo aquí
              $consultasRoute = route('moderador.inversiones.index'); // placeholder
            @endphp
            <a href="{{ $consultasRoute }}" class="dropdown-toggle no-arrow">
              <span class="micon fa fa-table"></span><span class="mtext">Consultas</span>
            </a>
          </li>

          <li><div class="sidebar-small-cap" style="padding:10px 16px;color:#94a3b8;">Extra</div></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="mobile-menu-overlay" id="overlay"></div>

  {{-- Contenido principal --}}
  <div class="main-container">
    @yield('content')
  </div>

  {{-- JS mínimo para abrir/cerrar paneles --}}
  <script>
    const btnOpenSidebar  = document.getElementById('btn-open-sidebar');
    const btnCloseSidebar = document.getElementById('btn-close-sidebar');
    const leftSidebar     = document.getElementById('left-sidebar');
    const overlay         = document.getElementById('overlay');

    const btnOpenRight  = document.getElementById('btn-open-right');
    const btnCloseRight = document.getElementById('btn-close-right');
    const rightSidebar  = document.getElementById('right-sidebar');

    function openSidebar(){ leftSidebar.classList.add('open'); overlay.classList.add('show'); }
    function closeSidebar(){ leftSidebar.classList.remove('open'); overlay.classList.remove('show'); }
    function openRight(){ rightSidebar.classList.add('open'); }
    function closeRight(){ rightSidebar.classList.remove('open'); }

    btnOpenSidebar?.addEventListener('click', openSidebar);
    btnCloseSidebar?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', ()=>{ closeSidebar(); closeRight(); });
    btnOpenRight?.addEventListener('click', openRight);
    btnCloseRight?.addEventListener('click', closeRight);

    document.getElementById('reset-settings')?.addEventListener('click', (e)=>{
      e.preventDefault();
      document.body.removeAttribute('data-header');
      document.body.removeAttribute('data-sidebar');
      alert('Ajustes restablecidos');
    });

    // Botones de estilo (demo)
    document.querySelector('.header-dark')?.addEventListener('click', ()=>document.querySelector('.header').style.background='#0f172a');
    document.querySelector('.header-white')?.addEventListener('click', ()=>document.querySelector('.header').style.background='#fff');
    document.querySelector('.sidebar-dark')?.addEventListener('click', ()=>leftSidebar.style.background='#0f172a');
    document.querySelector('.sidebar-light')?.addEventListener('click', ()=>leftSidebar.style.background='#fff');
  </script>
</body>
</html>
