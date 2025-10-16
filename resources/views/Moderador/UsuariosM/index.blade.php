@extends('layouts.moderador')

@section('title', 'Parámetros - Usuarios')

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
    <h2 class="h4">Inversionistas de la Empresa {{ $proyectoNombre ?? '—' }}</h2>
  </div>

  {{-- Tabla de usuarios --}}
  <div class="card mb-5">
    <div class="card-header">Usuarios</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Cédula</th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th>Fecha</th>
              <th>Municipio</th>
              <th>Rol</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($usuarios as $u)
            <tr>
              <td>{{ $u->ID_Usuario }}</td>
              <td>{{ $u->Nombre }}</td>
              <td>{{ $u->Apellido }}</td>
              <td>{{ $u->Telefono }}</td>
              <td>{{ $u->Correo }}</td>
              <td>{{ $u->Fecha }}</td>
              <td>{{ optional($u->municipio)->Nombre }}</td>
              <td>{{ optional($u->rol)->Nombre }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-4">No hay usuarios asociados a este proyecto.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if(method_exists($usuarios,'links'))
      <div class="card-footer">
        {{ $usuarios->links() }}
      </div>
    @endif
  </div>

  {{-- Formulario de creación (rol predeterminado: Inversionista) --}}
  <div class="mb-3">
    <h2 class="h5">Creación de Usuarios</h2>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('moderador.usuarios.store') }}">
        @csrf

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Cédula</label>
          <div class="col-sm-10">
            <input name="cedula" type="number" class="form-control" placeholder="33457" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Nombre</label>
          <div class="col-sm-10">
            <input name="nombre" type="text" class="form-control" placeholder="Johnny" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Apellido</label>
          <div class="col-sm-10">
            <input name="apellido" type="text" class="form-control" placeholder="Garzón" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Teléfono</label>
          <div class="col-sm-10">
            <input name="telefono" type="text" class="form-control" placeholder="322 2535668">
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Correo</label>
          <div class="col-sm-10">
            <input name="correo" type="email" class="form-control" placeholder="user@example.com">
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Contraseña</label>
          <div class="col-sm-10">
            <input name="contrasena" type="password" class="form-control" placeholder="password" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Confirmación</label>
          <div class="col-sm-10">
            <input name="contrasena_confirmation" type="password" class="form-control" placeholder="password" required>
          </div>
        </div>

        <div class="row mb-3">
          <label class="col-sm-2 col-form-label">Fecha de nacimiento</label>
          <div class="col-sm-10">
            <input name="fecha" type="date" class="form-control">
          </div>
        </div>

        {{-- Rol fijo a Inversionista (ID=3) según tu lógica; no mostramos el select --}}
        <input type="hidden" name="rol" value="3">

        <div class="row mb-4">
          <label class="col-sm-2 col-form-label">Ciudad</label>
          <div class="col-sm-10">
            <select name="ciudad" class="form-select" required>
              <option value="" selected disabled>Seleccione</option>
              @foreach ($municipios as $m)
                <option value="{{ $m->ID_Municipio }}">{{ $m->Nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>

      </form>
    </div>
  </div>

</div>
@endsection
