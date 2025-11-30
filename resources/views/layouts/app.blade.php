<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Panel')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    body { background-color: #f5f6fa; }
    .app-shell { min-height: 100vh; }
    .app-sidebar { width: 260px; }
  </style>
</head>
<body>
  <div class="app-shell">
    <x-role-navbar />

    <div class="d-flex">
      @if(View::hasSection('sidebar'))
        <aside class="app-sidebar bg-white border-end shadow-sm d-none d-md-block">
          @yield('sidebar')
        </aside>
      @endif

      <div class="flex-grow-1">
        <div class="d-flex justify-content-between align-items-center px-4 pt-3 pb-1">
          <div>
            @hasSection('page-title')
              <h1 class="h4 fw-semibold mb-1">@yield('page-title')</h1>
            @endif
            @hasSection('title')
              <p class="text-muted mb-0">@yield('title')</p>
            @endif
          </div>
          @hasSection('topbar-right')
            <div class="d-none d-md-block">
              @yield('topbar-right')
            </div>
          @endif
        </div>

        <div class="px-4 pb-4">
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
