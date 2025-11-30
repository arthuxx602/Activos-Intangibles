@php
  $rolId = session('rol');
  $roles = [
    1 => 'Administrador',
    2 => 'Moderador',
    3 => 'Inversionista',
  ];
  $rolNombre = $roles[$rolId] ?? 'Invitado';
  $nombre = session('nombre', '');
  $apellido = session('apellido', '');
  $nombreCompleto = trim("$nombre $apellido") ?: 'Usuario';
  $inicial = mb_strtoupper(mb_substr($nombreCompleto, 0, 1));
@endphp

<nav class="navbar navbar-light bg-white shadow-sm border-bottom mb-3">
  <div class="container-fluid d-flex justify-content-between align-items-center py-2">
    <div class="d-flex align-items-center gap-2">
      <span class="fw-semibold text-primary">Sipmaiputavale</span>
      <span class="text-secondary">=</span>
      <span class="fw-semibold text-dark">{{ $rolNombre }}</span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-circle border border-2 border-primary text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
        {{ $inicial }}
      </div>
      <div class="text-end">
        <div class="fw-semibold small text-uppercase text-primary mb-0">{{ $rolNombre }}</div>
        <div class="small text-muted">{{ $nombreCompleto }}</div>
      </div>
    </div>
  </div>
</nav>
