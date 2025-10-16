@extends('layouts.moderador')

@section('title','Inversiones')

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

  <div class="mb-4">
    <h2 class="h4">Inversiones — Proyecto: {{ $proyectoNombre ?? '—' }}</h2>
  </div>
  
  <div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h4 mb-0">Inversiones — Proyecto: {{ $proyectoNombre ?? '—' }}</h2>
  <div class="d-flex gap-2">
    <a class="btn btn-light" href="{{ route('moderador.inversiones.docs.index') }}">Ver inversiones con certificado</a>
    <a class="btn btn-primary" href="{{ route('moderador.inversiones.create') }}">Nueva inversión</a>
  </div>
</div>


  {{-- Tabla --}}
  <div class="card mb-5">
    <div class="card-header">Listado</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Monto</th>
              <th>Fecha</th>
              <th>Descripción</th>
              <th>Inversionista</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($inversiones as $inv)
            <tr>
              <td>{{ $inv->ID_Inversion }}</td>
              <td>{{ number_format($inv->Monto, 2) }}</td>
              <td>{{ \Illuminate\Support\Carbon::parse($inv->Fecha)->format('Y-m-d') }}</td>
              <td>{{ $inv->Descripcion }}</td>
              <td>
                @if($inv->usuario)
                  {{ $inv->usuario->Nombre }} {{ $inv->usuario->Apellido }}
                  ({{ $inv->usuario->ID_Usuario }})
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center py-4">Sin inversiones registradas.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if(method_exists($inversiones,'links'))
      <div class="card-footer">
        {{ $inversiones->links() }}
      </div>
    @endif
  </div>

  {{-- Formulario creación --}}
  <div class="mb-3">
    <h2 class="h5">Registrar inversión</h2>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('moderador.inversiones.store') }}">
        @csrf

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Monto</label>
          <div class="col-sm-10">
            <input name="monto" type="number" step="0.01" min="0" class="form-control" placeholder="0.00" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Fecha</label>
          <div class="col-sm-10">
            <input name="fecha" type="date" class="form-control" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Descripción</label>
          <div class="col-sm-10">
            <textarea name="descripcion" class="form-control" rows="2" placeholder="Opcional"></textarea>
          </div>
        </div>

        <div class="row mb-4">
          <label class="col-sm-2 col-form-label">Inversionista</label>
          <div class="col-sm-10">
            <select name="id_usuario" class="form-select" required>
              <option value="" selected disabled>Seleccione</option>
              @foreach ($usuariosProyecto as $u)
                <option value="{{ $u->ID_Usuario }}">
                  {{ $u->Nombre }} {{ $u->Apellido }} ({{ $u->ID_Usuario }})
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <button class="btn btn-primary" type="submit">Guardar</button>
      </form>
    </div>
  </div>

</div>
@endsection
