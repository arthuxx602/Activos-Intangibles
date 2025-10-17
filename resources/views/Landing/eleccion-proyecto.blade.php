@extends('layouts.blank') {{-- si no tienes layout, puedes quitar esta línea y usar HTML completo --}}

@section('title','Selección de proyecto')

@section('content')
<div class="login-header box-shadow">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="brand-logo">
      <h3 class="m-0">Selección de proyecto</h3>
    </div>
  </div>
</div>

<div class="register-page-wrap d-flex align-items-center flex-wrap justify-content-center">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md">
        <div class="login-box bg-white box-shadow border-radius-10">
          <div class="login-title">
            <h2 class="text-center text-primary">Proyectos</h2>
          </div>

          <h6 class="mb-20">Hemos encontrado más proyectos vinculados a tu cuenta</h6>

          <form id="formulario_seleccion_proyecto" method="POST" action="{{ route('proyectos.seleccionar.store') }}">
            @csrf

            <div class="form-group row">
              <label class="col-sm-12 col-md-2 col-form-label">Proyectos</label>
              <div class="col-sm-12 col-md-10">
                <select name="proyecto" id="proyecto" class="custom-select col-12">
                  <option value="">Seleccione…</option>
                  @foreach ($projects as $p)
                    <option value="{{ $p->ID_Proyecto }}">{{ $p->Nombre }}</option>
                  @endforeach
                </select>
                @error('proyecto')
                  <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
              </div>
            </div>

            <div class="row align-items-center">
              <div class="col-5">
                <div class="input-group mb-0">
                  <button type="submit" class="btn btn-primary btn-lg btn-block">Ingresar</button>
                </div>
              </div>
              <div class="col-2">
                <div class="font-16 weight-600 text-center" data-color="#707373">Ó</div>
              </div>
              <div class="col-5">
                <div class="input-group mb-0">
                  <form action="{{ route('logout') }}" method="POST" class="w-100">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-lg btn-block">Volver</button>
                  </form>
                </div>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
  {{-- Ajusta las rutas si moviste assets a /legacy --}}
  <link rel="stylesheet" href="{{ asset('legacy/vendors/styles/core.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/vendors/styles/icon-font.min.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/src/plugins/jquery-steps/jquery.steps.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/vendors/styles/style.css') }}">
  <link rel="stylesheet" href="{{ asset('legacy/src/styles/style.css') }}">
@endpush

@push('scripts')
  <script src="{{ asset('legacy/vendors/scripts/core.js') }}"></script>
  <script src="{{ asset('legacy/vendors/scripts/script.min.js') }}"></script>
  <script src="{{ asset('legacy/vendors/scripts/process.js') }}"></script>
  <script src="{{ asset('legacy/vendors/scripts/layout-settings.js') }}"></script>
  <script src="{{ asset('legacy/src/plugins/jquery-steps/jquery.steps.js') }}"></script>
  <script src="{{ asset('legacy/vendors/scripts/steps-setting.js') }}"></script>
@endpush
<form method="POST" action="{{ route('legacy.guardar-proyecto') }}">
  @csrf
  <select name="proyecto" required>
    @foreach($projects as $p)
      <option value="{{ $p->ID_Proyecto }}">{{ $p->Nombre }}</option>
    @endforeach
  </select>
  <button type="submit" class="btn btn-primary">Ingresar</button>
</form>