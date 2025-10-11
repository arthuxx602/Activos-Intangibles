@extends('layouts.app')

@section('title','Tipos de inversión')
@section('page-title','Tipos de inversión')

@push('styles')
  {{-- DataTables + Buttons (Bootstrap 5) --}}
  <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.0.7/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/r-3.0.2/datatables.min.css">
@endpush

@section('content')
<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title">Registrar nuevo tipo</h5>
    <form id="formTipo" class="row g-3" onsubmit="guardarTipo(event)">
      <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input name="Nombre" id="nombre" class="form-control" placeholder="Vivienda" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Descripción</label>
        <input name="Descripcion" id="descripcion" class="form-control" placeholder="Breve descripción...">
      </div>
      <div class="col-12 text-end">
        <button class="btn btn-primary"><i class="bi bi-save"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="card-title mb-0">Listado</h5>
      <div class="d-flex gap-2">
        <input id="fSearch" class="form-control form-control-sm" placeholder="Buscar...">
        <button class="btn btn-outline-secondary btn-sm" id="btnReload">
          <i class="bi bi-arrow-repeat"></i> Recargar
        </button>
      </div>
    </div>

    <div class="table-responsive">
      <table id="tablaTipos" class="table table-striped align-middle w-100">
        <thead class="table-light">
          <tr>
            <th style="width: 90px;">ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th class="text-end" style="width: 130px;">Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal de edición --}}
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="formEdit" class="modal-content" onsubmit="actualizarTipo(event)">
      <div class="modal-header">
        <h5 class="modal-title">Editar tipo de inversión</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editId">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input id="editNombre" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea id="editDescripcion" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
  {{-- DataTables + Buttons (JS) --}}
  <script src="https://cdn.datatables.net/v/bs5/dt-2.0.7/b-3.0.2/b-html5-3.0.2/b-print-3.0.2/r-3.0.2/datatables.min.js"></script>
  {{-- PDF export (pdfmake) --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

  <script>
  const API = '/api/tipos-inversion';
  let DT, modalEdit;

  document.addEventListener('DOMContentLoaded', () => {
    modalEdit = new bootstrap.Modal('#editModal');

    // Init DataTable vacío (poblamos luego)
    DT = new DataTable('#tablaTipos', {
      data: [],
      columns: [
        { data: 'ID_Tipo' },
        { data: 'Nombre' },
        { data: 'Descripcion',
          render: (d) => d || '' },
        { data: null, orderable:false, className:'text-end',
          render: (row) => `
            <div class="btn-group">
              <button class="btn btn-sm btn-outline-primary" onclick='editar(${row.ID_Tipo}, ${JSON.stringify(row.Nombre)}, ${JSON.stringify(row.Descripcion ?? '')})'>
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="eliminar(${row.ID_Tipo})">
                <i class="bi bi-trash"></i>
              </button>
            </div>`
        }
      ],
      responsive: true,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, -1],[10, 25, 50, 'Todos']],
      language: {
        url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/es-ES.json'
      },
      dom: 'Bfrtip',
      buttons: [
        { extend:'excelHtml5', text:'<i class="bi bi-file-earmark-excel"></i> Excel', className:'btn btn-success btn-sm' },
        { extend:'csvHtml5',   text:'<i class="bi bi-filetype-csv"></i> CSV', className:'btn btn-outline-secondary btn-sm' },
        { extend:'pdfHtml5',   text:'<i class="bi bi-filetype-pdf"></i> PDF', className:'btn btn-danger btn-sm',
          orientation:'landscape', pageSize:'A4' },
        { extend:'print',      text:'<i class="bi bi-printer"></i> Imprimir', className:'btn btn-outline-dark btn-sm' }
      ]
    });

    // búsqueda de cabecera
    document.querySelector('#fSearch').addEventListener('input', (e)=> {
      DT.search(e.target.value).draw();
    });

    document.querySelector('#btnReload').addEventListener('click', cargarTipos);

    // carga inicial
    cargarTipos();
  });

  async function cargarTipos() {
    try {
      const url = new URL(API, window.location.origin);
      // Si quieres buscar del lado del servidor, podrías agregar ?search=...
      const res = await fetch(url.toString());
      const data = await res.json();
      DT.clear();
      DT.rows.add(data).draw();
      showToast('Listado actualizado', 'info');
    } catch (e) {
      console.error(e);
      showToast('No se pudo cargar el listado', 'danger');
    }
  }

  async function guardarTipo(e) {
    e.preventDefault();
    const body = new URLSearchParams({
      Nombre: nombre.value,
      Descripcion: descripcion.value
    });

    const r = await fetch(API, { method:'POST', body });
    const j = await r.json().catch(()=>({}));
    if (r.ok) {
      showToast('Tipo registrado', 'success');
      e.target.reset();
      cargarTipos();
    } else {
      showToast(j.message || 'Error al guardar', 'danger');
    }
  }

  function editar(id, nombreTxt, descTxt) {
    editId.value = id;
    editNombre.value = nombreTxt;
    editDescripcion.value = descTxt;
    modalEdit.show();
  }

  async function actualizarTipo(e) {
    e.preventDefault();
    const id = editId.value;
    const body = new URLSearchParams({
      Nombre: editNombre.value,
      Descripcion: editDescripcion.value
    });
    const r = await fetch(`${API}/${id}`, { method:'PUT', body });
    const j = await r.json().catch(()=>({}));
    if (r.ok) {
      showToast('Tipo actualizado', 'success');
      modalEdit.hide();
      cargarTipos();
    } else {
      showToast(j.message || 'Error al actualizar', 'danger');
    }
  }

  async function eliminar(id) {
    if (!confirm('¿Eliminar este tipo de inversión?')) return;
    const r = await fetch(`${API}/${id}`, { method:'DELETE' });
    const j = await r.json().catch(()=>({}));
    if (r.ok) {
      showToast('Tipo eliminado', 'success');
      cargarTipos();
    } else {
      showToast(j.message || 'No se pudo eliminar', 'danger');
    }
  }
  </script>
@endpush
