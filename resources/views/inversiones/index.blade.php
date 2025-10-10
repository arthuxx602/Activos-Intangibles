@extends('layouts.app')

@section('title','Inversiones')
@section('page-title','Inversiones')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h3 class="mb-0">Inversiones</h3>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInv" onclick="abrirCrear()">
    <i class="bi bi-plus-lg me-1"></i> Nueva inversión
  </button>
</div>

<!-- FILTROS -->
<div class="card mb-3">
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-sm-3">
        <label class="form-label">Usuario</label>
        <select id="fUsuario" class="form-select"></select>
      </div>
      <div class="col-sm-3">
        <label class="form-label">Proyecto</label>
        <select id="fProyecto" class="form-select"></select>
      </div>
      <div class="col-sm-2">
        <label class="form-label">Tipo</label>
        <select id="fTipo" class="form-select"></select>
      </div>
      <div class="col-sm-2">
        <label class="form-label">Desde</label>
        <input id="fDesde" type="date" class="form-control">
      </div>
      <div class="col-sm-2">
        <label class="form-label">Hasta</label>
        <input id="fHasta" type="date" class="form-control">
      </div>

      <div class="col-12 d-flex gap-2">
        <input id="fSearch" type="text" class="form-control" placeholder="Buscar por nombre o descripción...">
        <button class="btn btn-outline-secondary" onclick="resetFiltros()">Limpiar</button>
        <button class="btn btn-primary" onclick="cargarInversiones()">Aplicar</button>
      </div>
    </div>
  </div>
</div>

<!-- TABLA -->
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle" id="tbl">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Proyecto</th>
            <th>Tipo</th>
            <th class="text-end">Monto</th>
            <th>Certificado</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
    </div>

    <!-- Paginación simple -->
    <div class="d-flex justify-content-between align-items-center mt-2">
      <div id="pageInfo" class="text-secondary small"></div>
      <div class="btn-group" role="group">
        <button class="btn btn-outline-secondary btn-sm" id="prevBtn" onclick="cambiarPagina(-1)">«</button>
        <button class="btn btn-outline-secondary btn-sm" id="nextBtn" onclick="cambiarPagina(1)">»</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL CREAR/EDITAR -->
<div class="modal fade" id="modalInv" tabindex="-1">
  <div class="modal-dialog">
    <form id="formInv" class="modal-content" enctype="multipart/form-data" onsubmit="guardar(event)">
      <div class="modal-header">
        <h5 class="modal-title" id="tituloModal">Nueva inversión</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="idInv">
        <div class="mb-2">
          <label class="form-label">Nombre</label>
          <input id="Nombre" name="Nombre" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Monto</label>
          <input id="Monto" name="Monto" type="number" step="0.01" min="0" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Fecha</label>
          <input id="Fecha" name="Fecha" type="date" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Usuario</label>
          <select id="FK_ID_Usuario" name="FK_ID_Usuario" class="form-select" required></select>
        </div>
        <div class="mb-2">
          <label class="form-label">Proyecto</label>
          <select id="FK_ID_Proyecto" name="FK_ID_Proyecto" class="form-select" required></select>
        </div>
        <div class="mb-2">
          <label class="form-label">Tipo</label>
          <select id="FK_ID_Tipo" name="FK_ID_Tipo" class="form-select" required></select>
        </div>
        <div class="mb-2">
          <label class="form-label">Descripción</label>
          <textarea id="Descripcion" name="Descripcion" class="form-control" rows="2"></textarea>
        </div>
        <div class="mb-2">
          <label class="form-label">Certificado (opcional)</label>
          <input id="CertificadoInversion" name="CertificadoInversion" type="file" class="form-control" accept=".pdf,.zip,.jpg,.jpeg,.png">
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

@section('scripts')
<script>
const API = '/api';
let pagination = { current_page: 1, last_page: 1 };

const $ = sel => document.querySelector(sel);
function fmtMoney(n){ return new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n||0); }

async function cargarCatalogos(){
  const u = await (await fetch(`${API}/catalogos/usuarios-para-vincular`)).json();
  const selU = $('#fUsuario'); selU.innerHTML = `<option value="">Todos</option>`;
  const selUF = $('#FK_ID_Usuario'); selUF.innerHTML = `<option value="">Seleccione...</option>`;
  u.forEach(x=>{
    const opt = new Option(`${x.ID_Usuario} - ${x.Nombre} ${x.Apellido}`, x.ID_Usuario);
    selU.add(opt.cloneNode(true));
    selUF.add(opt);
  });

  const p = await (await fetch(`${API}/proyectos?per_page=1000`)).json();
  const listP = p.data ?? [];
  const selP = $('#fProyecto'); selP.innerHTML = `<option value="">Todos</option>`;
  const selPF = $('#FK_ID_Proyecto'); selPF.innerHTML = `<option value="">Seleccione...</option>`;
  listP.forEach(x=>{
    const opt = new Option(`${x.ID_Proyecto} - ${x.Nombre}`, x.ID_Proyecto);
    selP.add(opt.cloneNode(true));
    selPF.add(opt);
  });

  const t = await (await fetch(`${API}/tipos-inversion?per_page=1000`)).json();
  const listT = t.data ?? t;
  const selT = $('#fTipo'); selT.innerHTML = `<option value="">Todos</option>`;
  const selTF = $('#FK_ID_Tipo'); selTF.innerHTML = `<option value="">Seleccione...</option>`;
  listT.forEach(x=>{
    const opt = new Option(x.Nombre, x.ID_TIPO);
    selT.add(opt.cloneNode(true));
    selTF.add(opt);
  });
}

function resetFiltros(){
  $('#fUsuario').value = '';
  $('#fProyecto').value = '';
  $('#fTipo').value = '';
  $('#fDesde').value = '';
  $('#fHasta').value = '';
  $('#fSearch').value = '';
  pagination.current_page = 1;
  cargarInversiones();
}

async function cargarInversiones(page = null){
  if(page) pagination.current_page = page;
  const params = new URLSearchParams();
  if($('#fUsuario').value)  params.set('usuario', $('#fUsuario').value);
  if($('#fProyecto').value) params.set('proyecto', $('#fProyecto').value);
  if($('#fTipo').value)     params.set('tipo', $('#fTipo').value);
  if($('#fDesde').value)    params.set('from', $('#fDesde').value);
  if($('#fHasta').value)    params.set('to', $('#fHasta').value);
  if($('#fSearch').value)   params.set('search', $('#fSearch').value);
  params.set('page', pagination.current_page);

  const res = await fetch(`${API}/inversiones?${params.toString()}`);
  const data = await res.json();

  const rows = data.data ?? data;
  const tbody = $('#tbody');
  tbody.innerHTML = '';

  rows.forEach(inv=>{
    const usuario = inv.usuario ? `${inv.usuario.ID_Usuario} - ${inv.usuario.Nombre}` : '-';
    const proyecto = inv.proyecto ? inv.proyecto.Nombre : (inv.Proyecto || '-');
    const tipo = inv.tipo ? inv.tipo.Nombre : '-';
    const cert = inv.CertificadoInversion ? `<a href="${API}/descargas/inversiones/${inv.ID_Inversion}/certificado" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-file-earmark-arrow-down"></i></a>` : '';

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${inv.Fecha ?? ''}</td>
      <td>${usuario}</td>
      <td>${proyecto}</td>
      <td>${tipo}</td>
      <td class="text-end">${fmtMoney(inv.Monto)}</td>
      <td>${cert}</td>
      <td class="text-end">
        <div class="btn-group">
          <button class="btn btn-sm btn-outline-primary" onclick='abrirEditar(${JSON.stringify(inv)})'><i class="bi bi-pencil-square"></i></button>
          <button class="btn btn-sm btn-outline-danger"  onclick="eliminar(${inv.ID_Inversion})"><i class="bi bi-trash"></i></button>
        </div>
      </td>`;
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
  cargarInversiones(next);
}

let modal;
document.addEventListener('DOMContentLoaded', async ()=>{
  modal = new bootstrap.Modal('#modalInv');
  await cargarCatalogos();
  await cargarInversiones();
});

function abrirCrear(){
  $('#tituloModal').textContent = 'Nueva inversión';
  $('#idInv').value = '';
  $('#formInv').reset();
}

function abrirEditar(inv){
  $('#tituloModal').textContent = 'Editar inversión';
  $('#idInv').value = inv.ID_Inversion;
  $('#Nombre').value = inv.Nombre ?? '';
  $('#Monto').value = inv.Monto ?? '';
  $('#Fecha').value = inv.Fecha ?? '';
  $('#FK_ID_Usuario').value = inv.FK_ID_Usuario ?? (inv.usuario?.ID_Usuario ?? '');
  $('#FK_ID_Proyecto').value = inv.FK_ID_Proyecto ?? (inv.proyecto?.ID_Proyecto ?? '');
  $('#FK_ID_Tipo').value = inv.FK_ID_Tipo ?? (inv.tipo?.ID_TIPO ?? '');
  $('#Descripcion').value = inv.Descripcion ?? '';
  $('#CertificadoInversion').value = '';
  modal.show();
}

async function guardar(e){
  e.preventDefault();
  const id = $('#idInv').value;
  const fd = new FormData($('#formInv'));

  const url = id ? `${API}/inversiones/${id}` : `${API}/inversiones`;
  const method = id ? 'PUT' : 'POST';

  const res = await fetch(url, { method, body: fd });
  if(!res.ok){
    const err = await res.json().catch(()=> ({}));
    alert('Error al guardar: ' + (err.message || res.status));
    return;
  }
  modal.hide();
  await cargarInversiones();
}

async function eliminar(id){
  if(!confirm('¿Eliminar inversión?')) return;
  const res = await fetch(`${API}/inversiones/${id}`, { method:'DELETE' });
  if(!res.ok){ alert('No se pudo eliminar'); return; }
  await cargarInversiones();
}
</script>
@endsection
