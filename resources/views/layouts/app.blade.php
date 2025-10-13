<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Panel')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap + Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- Vite --}}
  @vite(['resources/css/app.css','resources/js/app.js'])

  <style>
    :root{
      --sidebar-bg:#0f172a;--sidebar-hover:#1e293b;--sidebar-text:#cbd5e1;
      --divider:#e5e7eb;--topbar-bg:#fff;--brand:#38bdf8;--pill:#0ea5e9;
    }
    html,body{height:100%;background:#f7f9fb}
    body{font-family:system-ui,-apple-system,'Segoe UI',Roboto,Inter,"Helvetica Neue","Noto Sans",Arial,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol"}
    .sidebar{position:fixed;top:0;bottom:0;left:0;width:260px;background:var(--sidebar-bg);color:#fff;z-index:1030;display:flex;flex-direction:column}
    .sidebar .brand{padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.08);font-weight:700;display:flex;align-items:center;gap:.5rem}
    .sidebar nav{padding:12px;overflow:auto}
    .sidebar .section-label{color:#94a3b8;font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;margin:10px 6px 6px;display:block}
    .sidebar a{display:flex;align-items:center;gap:.6rem;color:var(--sidebar-text);text-decoration:none;padding:.56rem .7rem;border-radius:.55rem;font-size:.95rem}
    .sidebar a:hover,.sidebar a.active{background:var(--sidebar-hover);color:#fff}
    .content{margin-left:260px;min-height:100vh;display:flex;flex-direction:column}
    .topbar{background:var(--topbar-bg);border-bottom:1px solid var(--divider);height:58px;display:flex;align-items:center;justify-content:space-between;padding:0 14px}
    .user-badge{width:36px;height:36px;border-radius:50%;background:var(--pill);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700}
    .container-page{flex:1;padding:18px 20px 28px}
    #toastPortal{position:fixed;right:16px;top:16px;z-index:1080;display:flex;flex-direction:column;gap:8px}
    @media(max-width:991.98px){.sidebar{transform:translateX(-101%);transition:transform .25s ease}.sidebar.open{transform:translateX(0)}.content{margin-left:0}}
  </style>

  @yield('head')
</head>
<body>
<aside class="sidebar" id="appSidebar">
  <div class="brand"><i class="bi bi-grid-1x2 text-info"></i><span>SIPMAINPUTVALUE</span></div>
  <nav>
    <span class="section-label">Menú</span>
    <a href="{{ route('inicio') }}" class="{{ request()->routeIs('inicio') ? 'active' : '' }}"><i class="bi bi-house-door"></i><span>Inicio</span></a>

    <span class="section-label mt-3">Parámetros</span>
    <a href="{{ route('empresas.index') }}"  class="{{ request()->routeIs('empresas.*') ? 'active' : '' }}"><i class="bi bi-building"></i><span>Empresas</span></a>
    <a href="{{ route('usuarios.index') }}"  class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Usuarios</span></a>
    <a href="{{ route('ubicacion.index') }}" class="{{ request()->routeIs('ubicacion.*') ? 'active' : '' }}"><i class="bi bi-geo-alt"></i><span>Ubicación</span></a>
    <a href="{{ route('inversiones.index') }}" class="{{ request()->routeIs('inversiones.*') ? 'active' : '' }}"><i class="bi bi-cash-coin"></i><span>Inversiones</span></a>
    <a href="{{ route('tasas.index') }}" class="{{ request()->routeIs('tasas.*') ? 'active' : '' }}"><i class="bi bi-percent"></i><span>Tasas de interés</span></a>

    <span class="section-label mt-3">Cálculo</span>
    <a href="{{ route('simulacion.index') }}" class="{{ request()->routeIs('simulacion.*') ? 'active' : '' }}"><i class="bi bi-pie-chart"></i><span>Simulación</span></a>
    <a href="{{ route('liquidacion.index') }}" class="{{ request()->routeIs('liquidacion.*') ? 'active' : '' }}"><i class="bi bi-balance-scale"></i><span>Distribuir participación</span></a>

    <span class="section-label mt-3">Consultas</span>
    <a href="{{ route('consultas.index') }}" class="{{ request()->routeIs('consultas.*') ? 'active' : '' }}"><i class="bi bi-table"></i><span>Consultas</span></a>
  </nav>
</aside>

<div class="content">
  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" id="btnSidebar"><i class="bi bi-list"></i></button>
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
    {{-- Portal con datos flash en atributos --}}
    <div id="toastPortal"
         aria-live="polite"
         aria-atomic="true"
         data-success='@json(session("success"))'
         data-error='@json(session("error"))'></div>

    @yield('content')
  </main>

  <footer class="px-3 py-2 small text-secondary">© {{ date('Y') }} SIPMAINPUTVALUE</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('btnSidebar')?.addEventListener('click', () =>
  document.getElementById('appSidebar')?.classList.toggle('open')
);

(function(){
  function showToast(message,{variant='primary',delay=3500}={}){
    const portal=document.getElementById('toastPortal');
    if(!portal)return;
    const wrap=document.createElement('div');
    wrap.innerHTML=`
      <div class="toast align-items-center text-bg-${variant} border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>`;
    const toastEl=wrap.firstElementChild;
    portal.appendChild(toastEl);
    const t=new bootstrap.Toast(toastEl,{delay});
    toastEl.addEventListener('hidden.bs.toast',()=>toastEl.remove());
    t.show();
  }
  window.showToast=showToast;

  const portal=document.getElementById('toastPortal');
  const s=portal?.dataset.success?JSON.parse(portal.dataset.success):null;
  const e=portal?.dataset.error?JSON.parse(portal.dataset.error):null;
  if(s)showToast(s,{variant:'success'});
  if(e)showToast(e,{variant:'danger'});
})();
</script>

@yield('scripts')
</body>
</html>
