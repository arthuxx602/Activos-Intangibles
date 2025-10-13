@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Usuarios</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUser" onclick="abrirCrear()">
      <i class="bi bi-plus-lg me-1"></i> Nuevo usuario
    </button>
  </div>

  <!-- FILTROS -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-sm-3">
          <label class="form-label">Buscar</label>
          <input id="fSearch" type="text" class="form-control" placeholder="Nombre, cédula, email…">
        </div>
        <div class="col-sm-3">
          <label class="form-label">Municipio</label>
          <select id="fMunicipio" class="form-select"></select>
        </div>
        <div class="col-sm-3">
          <label class="form-label">Rol</label>
          <select id="fRol" class="form-select"></select>
        </div>
        <div class="col-sm-3">
          <label class="form-label">Desde</label>
          <input id="fDesde" type="date" class="form-control">
        </div>
        <div class="col-sm-3">
          <label class="form-label">Hasta</label>
          <input id="fHasta" type="date" class="form-control">
        </div>

        <div class="col-12 d-flex gap-2">
          <button class="btn btn-outline-secondary" onclick="resetFiltros()">Limpiar</button>
          <button class="btn btn-primary" onclick="cargarUsuarios()">Aplicar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- TABLA -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Cédula</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Email</th>
              <th>Fecha</th>
              <th>Municipio</th>
              <th>Rol</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-2">
        <div id="pageInfo" class="text-secondary small"></div>
        <div class="btn-group" role="group">
          <button class="btn btn-outline-secondary btn-sm" id="prevBtn" onclick="cambiarPagina(-1)">«</button>
          <button class="btn btn-outline-secondary btn-sm" id="nextBtn" onclick="cambiarPagina(1)">»</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL CREAR/EDITAR -->
<div class="modal fade" id="modalUser" tabindex="-1">
  <div class="modal-dialog">
    <form id="formUser" class="modal-content" onsubmit="guardar(event)">
      <div class="modal-header">
        <h5 class="modal-title" id="tituloModal">Nuevo usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="idUsuario"> {{-- PK (cuando editemos) --}}
        <div class="row g-2">
          <div class="col-sm-6">
            <label class="form-label">Cédula</label>
            <input id="ID_Usuario" name="ID_Usuario" type="number" class="form-control" required>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Fecha</label>
            <input id="Fecha" name="Fecha" type="date" class="form-control" required>
          </div>
        </div>

        <div class="row g-2 mt-1">
          <div class="col-sm-6">
            <label class="form-label">Nombre</label>
            <input id="Nombre" name="Nombre" class="form-control" required>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Apellido</label>
            <input id="Apellido" name="Apellido" class="form-control" required>
          </div>
        </div>

        <div class="row g-2 mt-1">
          <div class="col-sm-6">
            <label class="form-label">Teléfono</label>
            <input id="Telefono" name="Telefono" class="form-control" required>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Correo</label>
            <input id="Correo" name="Correo" type="email" class="form-control" required>
          </div>
        </div>

        <div class="mt-2">
          <label class="form-label">Contraseña</label>
          <input id="Contraseña" name="Contraseña" type="text" class="form-control" required>
          <div class="form-text">*Se mantiene el campo legado (sin hash) para compatibilidad.</div>
        </div>

        <div class="row g-2 mt-1">
          <div class="col-sm-6">
            <label class="form-label">Municipio</label>
            <select id="FK_ID_Municipio" name="FK_ID_Municipio" class="form-select" required></select>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Rol</label>
            <select id="FK_ID_Rol" name="FK_ID_Rol" class="form-select" required></select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const API = '/api';
let modal, pagination = { current_page: 1, last_page: 1 };

// helpers
const $ = s => document.querySelector(s);
const fmtDate = s => s ?? '';
function fullName(u){ return `${u.Nombre ?? ''} ${u.Apellido ?? ''}`.trim(); }

async function cargarCatalogos() {
  // Municipios
  const m = await (await fetch(`${API}/municipios?per_page=1000`)).json();
  const municipios = m.data ?? m;
  const selFM = $('#fMunicipio'); selFM.innerHTML = `<option value="">Todos</option>`;
  const selM  = $('#FK_ID_Municipio'); selM.innerHTML = `<option value="">Seleccione...</option>`;
  municipios.forEach(x => {
    const opt = new Option(`${x.Nombre}`, x.ID_Municipio);
    selFM.add(opt.cloneNode(true));
    selM.add(opt);
  });

  // Roles
  const r = await (await fetch(`${API}/catalogos/roles`)).json();
  const selFR = $('#fRol'); selFR.innerHTML = `<option value="">Todos</option>`;
  const selR  = $('#FK_ID_Rol'); selR.innerHTML = `<option value="">Seleccione...</option>`;
  r.forEach(x => {
    const opt = new Option(x.Nombre, x.ID_Rol);
    selFR.add(opt.cloneNode(true));
    selR.add(opt);
  });
}

function resetFiltros(){
  $('#fSearch').value = '';
  $('#fMunicipio').value = '';
  $('#fRol').value = '';
  $('#fDesde').value = '';
  $('#fHasta').value = '';
  pagination.current_page = 1;
  cargarUsuarios();
}

async function cargarUsuarios(page = null){
  if(page) pagination.current_page = page;

  const params = new URLSearchParams();
  if($('#fSearch').value)    params.set('search', $('#fSearch').value);
  if($('#fMunicipio').value) params.set('municipio', $('#fMunicipio').value);
  if($('#fRol').value)       params.set('rol', $('#fRol').value);
  if($('#fDesde').value)     params.set('from', $('#fDesde').value);
  if($('#fHasta').value)     params.set('to', $('#fHasta').value);
  params.set('page', pagination.current_page);

  const res = await fetch(`${API}/usuarios?${params.toString()}`);
  if(!res.ok){ window.showToast('Error al cargar usuarios','danger'); return; }
  const data = await res.json();

  const rows = data.data ?? data;
  const tbody = $('#tbody');
  tbody.innerHTML = '';
  rows.forEach(u => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${u.ID_Usuario}</td>
      <td>${fullName(u)}</td>
      <td>${u.Telefono ?? ''}</td>
      <td>${u.Correo ?? ''}</td>
      <td>${fmtDate(u.Fecha)}</td>
      <td>${u.municipio?.Nombre ?? '-'}</td>
      <td>${u.rol?.Nombre ?? '-'}</td>
      <td class="text-end">
        <div class="btn-group">
          <button class="btn btn-sm btn-outline-primary" onclick='abrirEditar(${JSON.stringify(u)})'><i class="bi bi-pencil-square"></i></button>
          <button class="btn btn-sm btn-outline-danger"  onclick="eliminar(${u.ID_Usuario})"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });

  if(data.current_page){
    pagination.current_page = data.current_page;
    pagination.last_page = data.last_page;
    $('#pageInfo').textContent = `Página ${data.current_page} de ${data.last_page} • ${data.total} registros`;
    $('#prevBtn').disabled = (data.current_page <= 1);
    $('#nextBtn').disabled = (data.current_page >= data.last_page);
  } else {
    $('#pageInfo').textContent = `${rows.length} registros`;
    $('#prevBtn').disabled = true; $('#nextBtn').disabled = true;
  }
}

function cambiarPagina(delta){
  const next = pagination.current_page + delta;
  if(next < 1 || next > pagination.last_page) return;
  cargarUsuarios(next);
}

// modal
function abrirCrear(){
  $('#tituloModal').textContent = 'Nuevo usuario';
  $('#idUsuario').value = '';
  $('#ID_Usuario').value = '';
  $('#Nombre').value = '';
  $('#Apellido').value = '';
  $('#Telefono').value = '';
  $('#Correo').value = '';
  $('#Contraseña').value = '';
  $('#Fecha').value = '';
  $('#FK_ID_Municipio').value = '';
  $('#FK_ID_Rol').value = '';
}

function abrirEditar(u){
  $('#tituloModal').textContent = 'Editar usuario';
  $('#idUsuario').value = u.ID_Usuario;
  $('#ID_Usuario').value = u.ID_Usuario;         // PK, lo bloqueamos visualmente
  $('#ID_Usuario').setAttribute('readonly', 'readonly');
  $('#Nombre').value = u.Nombre ?? '';
  $('#Apellido').value = u.Apellido ?? '';
  $('#Telefono').value = u.Telefono ?? '';
  $('#Correo').value = u.Correo ?? '';
  $('#Contraseña').value = u.Contraseña ?? '';    // legacy
  $('#Fecha').value = (u.Fecha ?? '').substring(0,10);
  $('#FK_ID_Municipio').value = u.FK_ID_Municipio ?? (u.municipio?.ID_Municipio ?? '');
  $('#FK_ID_Rol').value = u.FK_ID_Rol ?? (u.rol?.ID_Rol ?? '');
  modal.show();
}

async function guardar(e){
  e.preventDefault();
  const id = $('#idUsuario').value;
  const payload = {
    ID_Usuario:      $('#ID_Usuario').value,
    Nombre:          $('#Nombre').value,
    Apellido:        $('#Apellido').value,
    Telefono:        $('#Telefono').value,
    Correo:          $('#Correo').value,
    Contraseña:      $('#Contraseña').value,
    Fecha:           $('#Fecha').value,
    FK_ID_Municipio: $('#FK_ID_Municipio').value,
    FK_ID_Rol:       $('#FK_ID_Rol').value,
  };

  let url = `${API}/usuarios`;
  let method = 'POST';
  if(id){
    url = `${API}/usuarios/${id}`;
    method = 'PUT';
    delete payload.ID_Usuario; // no permitir cambiar PK
  }

  const res = await fetch(url, {
    method,
    headers: { 'Content-Type':'application/json' },
    body: JSON.stringify(payload)
  });

  if(!res.ok){
    const err = await res.json().catch(()=> ({}));
    window.showToast(err.message || 'Error al guardar', 'danger');
    return;
  }
  modal.hide();
  window.showToast('Guardado correctamente', 'success');
  $('#ID_Usuario').removeAttribute('readonly');
  await cargarUsuarios();
}

async function eliminar(id){
  if(!confirm('¿Eliminar usuario?')) return;
  const res = await fetch(`${API}/usuarios/${id}`, { method:'DELETE' });
  if(!res.ok){ window.showToast('No se pudo eliminar','danger'); return; }
  window.showToast('Usuario eliminado','success');
  await cargarUsuarios();
}

// init
document.addEventListener('DOMContentLoaded', async ()=>{
  modal = new bootstrap.Modal('#modalUser');
  await cargarCatalogos();
  await cargarUsuarios();
});
</script>
@endpush
