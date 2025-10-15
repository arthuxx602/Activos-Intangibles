@extends('layouts.app')

@section('title', 'Tipos de Inversiones')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4">Tipos de Inversiones</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Tabla -->
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tipos as $tipo)
                        <tr>
                            <td>{{ $tipo->Nombre }}</td>
                            <td>{{ $tipo->Descripcion }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="{{ $tipo->ID_TIPO }}"
                                    data-nombre="{{ $tipo->Nombre }}"
                                    data-descripcion="{{ $tipo->Descripcion }}">
                                    Editar
                                </button>

                                <form action="{{ route('tipos.destroy', $tipo->ID_TIPO) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Seguro deseas eliminar este tipo?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulario Crear -->
    <div class="card mb-5">
        <div class="card-header">Crear Tipo de Inversión</div>
        <div class="card-body">
            <form action="{{ route('tipos.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre_tipo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion_tipo" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>

</div>

<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="editForm">
        @csrf
        @method('PUT')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Tipo de Inversión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre_tipo" id="nombre_tipo" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion_tipo" id="descripcion_tipo" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        const descripcion = button.getAttribute('data-descripcion');

        const form = document.getElementById('editForm');
        form.action = `/tipos/${id}`;
        document.getElementById('nombre_tipo').value = nombre;
        document.getElementById('descripcion_tipo').value = descripcion;
    });
</script>
@endsection
