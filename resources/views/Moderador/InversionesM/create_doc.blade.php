@extends('layouts.moderador')
@section('title','Registrar inversión (con PDF)')
@extends('layouts.moderador')
@section('title','Inversiones con certificado')

@section('content')
<div class="container-fluid">

  @if (session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 mb-0">Inversiones con certificado — Proyecto: {{ $proyectoNombre ?? '—' }}</h2>
    <div class="d-flex gap-2">
      <a class="btn btn-light" href="{{ route('moderador.inversiones.index') }}">Ver inversiones (tabla principal)</a>
      <a class="btn btn-primary" href="{{ route('moderador.inversiones.docs.create') }}">Registrar con PDF</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Usuario (texto)</th>
              <th>Monto</th>
              <th>Proyecto (nombre)</th>
              <th>Tipo</th>
              <th>Fecha</th>
              <th>Descripción</th>
              <th class="text-end">Certificado</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($inversiones as $inv)
            <tr>
              <td>{{ $inv->ID_Inversion2 }}</td>
              <td>{{ $inv->Nombre }}</td>
              <td>{{ number_format($inv->Monto, 2) }}</td>
              <td>{{ $inv->proyecto }}</td>
              <td>{{ $inv->Tipo }}</td>
              <td>{{ \Illuminate\Support\Carbon::parse($inv->Fecha)->format('Y-m-d') }}</td>
              <td>{{ $inv->Descripcion }}</td>
              <td class="text-end">
                @if($inv->CertificadoInversion)
                  {{-- Ver (nueva pestaña) --}}
                  <a class="btn btn-sm btn-outline-secondary"
                     target="_blank"
                     href="{{ asset('storage/certificados/'.$inv->CertificadoInversion) }}">
                     Ver
                  </a>
                  {{-- Descargar --}}
                  <a class="btn btn-sm btn-outline-primary"
                     href="{{ asset('storage/certificados/'.$inv->CertificadoInversion) }}"
                     download>
                     Descargar
                  </a>
                @else
                  <span class="text-muted">No disponible</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center py-4">Sin inversiones con certificado.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer">
      {{ $inversiones->links() }}
    </div>
  </div>

</div>
@endsection


@section('content')
<div class="container-fluid">

  @if (session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="mb-3">
    <h2 class="h4">Registrar inversión — Proyecto activo: {{ $proyectoNombre ?? '—' }}</h2>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data"
            action="{{ route('moderador.inversiones.docs.store') }}">
        @csrf

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Usuario (nombre)</label>
          <div class="col-sm-10">
            <input name="usuario" type="text" class="form-control"
                   value="{{ old('usuario') }}" placeholder="Nombre visible en el certificado" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Monto</label>
          <div class="col-sm-10">
            <input name="monto" type="number" step="0.01" min="0"
                   class="form-control" value="{{ old('monto') }}" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Proyecto (por nombre)</label>
          <div class="col-sm-10">
            {{-- Igual que legacy: guardas el NOMBRE del proyecto --}}
            <input name="proyecto" list="proyectos" class="form-control"
                   value="{{ old('proyecto') }}" placeholder="Escribe o elige el nombre" required>
            <datalist id="proyectos">
              @foreach($proyectos as $p)
                <option value="{{ $p->Nombre }}"></option>
              @endforeach
            </datalist>
            <small class="text-muted">Se validará que el nombre exista en la tabla proyecto.</small>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Tipo (texto)</label>
          <div class="col-sm-10">
            <input name="tipo" type="text" class="form-control"
                   value="{{ old('tipo') }}" placeholder="Ej: Aporte, Donación, etc." required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Fecha inversión</label>
          <div class="col-sm-10">
            <input name="fecha_inversion" type="date" class="form-control"
                   value="{{ old('fecha_inversion') }}" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Descripción</label>
          <div class="col-sm-10">
            <textarea name="descripcion_inversion" rows="2" class="form-control" required>{{ old('descripcion_inversion') }}</textarea>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Inversionista (ID_Usuario)</label>
          <div class="col-sm-10">
            <select name="id_usuario" class="form-select" required>
              <option value="" disabled selected>Seleccione</option>
              @foreach ($usuariosProyecto as $u)
                <option value="{{ $u->ID_Usuario }}"
                  {{ old('id_usuario') == $u->ID_Usuario ? 'selected' : '' }}>
                  {{ $u->Nombre }} {{ $u->Apellido }} ({{ $u->ID_Usuario }})
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row mb-4">
          <label class="col-sm-2 col-form-label">Tipo (ID_Tipo)</label>
          <div class="col-sm-10">
            <select name="id_tipo" class="form-select" required>
              <option value="" disabled selected>Seleccione</option>
              @foreach ($tipos as $t)
                <option value="{{ $t->ID_Tipo }}"
                  {{ old('id_tipo') == $t->ID_Tipo ? 'selected' : '' }}>
                  {{ $t->Nombre }} ({{ $t->ID_Tipo }})
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="row mb-4">
          <label class="col-sm-2 col-form-label">Certificado (PDF)</label>
          <div class="col-sm-10">
            <input name="archivo" type="file" class="form-control" accept="application/pdf" required>
            <small class="text-muted">Solo PDF. Máx 10MB.</small>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-primary" type="submit">Guardar</button>
          <a class="btn btn-light" href="{{ route('moderador.inversiones.index') }}">Cancelar</a>
        </div>

      </form>
    </div>
  </div>

</div>
@endsection
