@extends('layouts.app')

@section('title','Inversionista')
@section('page-title','Inversionista')

{{-- Menú lateral exclusivo del módulo --}}
@section('sidebar')
  <div class="brand d-flex align-items-center gap-2">
    <i class="bi bi-person-badge"></i> Inversionista
  </div>
  <nav class="p-2">
    <div class="text-uppercase text-secondary small px-2 mt-2 mb-1">Menú</div>

    <a href="{{ route('inversionista.inicio') }}"
       class="{{ request()->routeIs('inversionista.inicio') ? 'active' : '' }}">
      <i class="bi bi-house-door me-2"></i> Inicio
    </a>

    <a href="{{ route('inversionista.resumen') }}"
       class="{{ request()->routeIs('inversionista.resumen') ? 'active' : '' }}">
      <i class="bi bi-table me-2"></i> Resumen
    </a>
  </nav>
@endsection

{{-- Parte derecha del topbar (inicial + nombre y logout opcional) --}}
@section('topbar-right')
  @php
    $nombre = session('nombre', 'Usuario');
    $apellido = session('apellido', '');
    $letra = mb_strtoupper(mb_substr($nombre, 0, 1));
  @endphp

  <div class="dropdown">
    <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
      <div class="user-badge">{{ $letra }}</div>
      <span class="small">{{ $nombre.' '.$apellido }}</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      {{-- Ajusta la ruta de logout si ya tienes auth --}}
      <li><a class="dropdown-item" href="{{ url('/logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
    </ul>
  </div>
@endsection

{{-- Contenido real de cada pantalla del módulo --}}
@section('content')
  @yield('inversionista-content')
@endsection
