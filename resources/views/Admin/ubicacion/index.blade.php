@extends('layouts.app')

@section('title', 'Ubicación')

@section('content')
<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Ubicación (Países, Departamentos y Municipios)</h4>
  </div>

  {{-- === PAISES === --}}
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold">Países</span>
      <button class="btn btn-sm btn-primary" onclick="modalPaisCrear()">
        <i class="bi bi-plus-lg me-1"></i> Nuevo país
      </button>
    </div>
    <div class="card-body">
      <div class="row g-2 mb-2">
        <div class="col-sm-6 col-md-4">
          <input id="searchPais" class="form-control form-control-sm" placeholder="Buscar país...">
        </div>
        <div class="col-auto">
          <button class="btn btn-sm btn-outline-secondary" onclick="cargarPaises()">Buscar</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr>
              <th style="width:110px">ID</th>
              <th>Nombre</th>
              <th class="text-end" style="width:140px">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbPaises"></tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- === DEPARTAMENTOS === --}}
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold">Departamentos / Provincias</span>
      <button class="btn btn-sm btn-primary" onclick="modalDepCrear()">
        <i class="bi bi-plus-lg me-1"></i> Nuevo departamento
      </button>
    </div>
    <div class="card-body">
      <div class="row g-2 mb-2">
        <div class="col-sm-4">
          <select id="fDepPais" class="form-select form-select-sm"></select>
        </div>
        <div class="col-sm-4">
          <input id="searchDep" class="form-control form-control-sm" placeholder="Buscar departamento...">
        </div>
        <div class="col-auto">
          <button class="btn btn-sm btn-outline-secondary" onclick="cargarDepartamentos()">Aplicar</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>País</th>
              <th class="text-end" style="width:140px">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbDepartamentos"></tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- === MUNICIPIOS === --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold">Municipios / Ciudades</span>
      <button class="btn btn-sm btn-primary" onclick="modalMunCrear()">
        <i class="bi bi-plus-lg me-1"></i> Nuevo municipio
      </button>
    </div>
    <div class="card-body">
      <div class="row g-2 mb-2">
        <div class="col-sm-4">
          <select id="fMunDep" class="form-select form-select-sm"></select>
        </div>
        <div class="col-sm-4">
          <input id="searchMun" class="form-control form-control-sm" placeholder="Buscar municipio...">
        </div>
        <div class="col-auto">
          <button class="btn btn-sm btn-outline-secondary" onclick="cargarMunicipios()">Aplicar</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Departamento</th>
              <th class="text-end" style="width:140px">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbMunicipios"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ============== MODALES ============== --}}
{{-- País --}}
<div class="modal fade" id="mdlPais" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" onsubmit="guardarPais(event)">
      <div class="modal-header">
        <h5 class="modal-title" id="ttlPais">Nuevo país</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="paisId">
        <div id="wrapIdPais" class="mb-2">
          <label class="form-label">Prefijo (ID_Pais)</label>
          <input id="ID_Pais" type="number" class="form-control" placeholder="01, 57..." min="0">
        </div>
        <div class="mb-2">
          <label class="form-label">Nombre</label>
          <input id="NombrePais" class="form-control" placeholder="Colombia" required>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>

{{-- Departamento --}}
<div class="modal fade" id="mdlDep" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" onsubmit="guardarDep(event)">
      <div class="modal-header">
        <h5 class="modal-title" id="ttlDep">Nuevo departamento</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="depId">
        <div class="mb-2">
          <label class="form-label">Nombre</label>
          <input id="NombreDep" class="form-control" placeholder="Cundinamarca" required>
        </div>
        <div class="mb-2">
          <label class="form-label">País</label>
          <select id="FK_ID_Pais" class="form-select" required></select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>

{{-- Municipio --}}
<div class="modal fade" id="mdlMun" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" onsubmit="guardarMun(event)">
      <div class="modal-header">
        <h5 class="modal-title" id="ttlMun">Nuevo municipio</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="munId">
        <div class="mb-2">
          <label class="form-label">Nombre</label>
          <input id="NombreMun" class="form-control" placeholder="Fusagasugá" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Departamento</label>
          <select id="FK_ID_Departamento" class="form-select" required></select>
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

// util
const $ = sel => document.querySelector(sel);
const createOpt = (text, val) => {
  const o = document.createElement('option'); o.textContent = text; o.value = val; return o;
};

// ========== PAISES ==========
let mdlPais;
function modalPaisCrear(){
  $('#ttlPais').textContent='Nuevo país';
  $('#paisId').value='';
  $('#ID_Pais').value='';
  $('#NombrePais').value='';
  $('#wrapIdPais').style.display='';
  mdlPais.show();
}
function modalPaisEditar(p){
  $('#ttlPais').textContent='Editar país';
  $('#paisId').value=p.ID_Pais;
  $('#ID_Pais').value=p.ID_Pais;
  $('#NombrePais').value=p.Nombre;
  $('#wrapIdPais').style.display='none'; // ID_Pais no editable
  mdlPais.show();
}
async function guardarPais(e){
  e.preventDefault();
  const id = $('#paisId').value;
  const payload = { Nombre: $('#NombrePais').value.trim() };
  let url = `${API}/paises`, method = 'POST';
  if(!id){
    payload.ID_Pais = Number($('#ID_Pais').value);
  } else {
    url = `${API}/paises/${id}`; method = 'PUT';
  }
  const res = await fetch(url, { method, headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
  if(!res.ok){ window.showToast('Error al guardar país','danger'); return; }
  mdlPais.hide(); window.showToast('País guardado','success'); await cargarPaises(); await poblarSelects();
}
async function borrarPais(id){
  if(!confirm('¿Eliminar país?')) return;
  const res = await fetch(`${API}/paises/${id}`, { method:'DELETE' });
  if(!res.ok){ window.showToast('No se pudo eliminar','danger'); return; }
  window.showToast('País eliminado','success'); await cargarPaises(); await poblarSelects();
}
async function cargarPaises(){
  const s = $('#searchPais').value?.trim() || '';
  const url = new URL(`${location.origin}${API}/paises`);
  if(s) url.searchParams.set('search', s);
  const data = await (await fetch(url)).json();
  const tb = $('#tbPaises'); tb.innerHTML='';
  data.forEach(p=>{
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td>+${p.ID_Pais}</td>
      <td>${p.Nombre}</td>
      <td class="text-end">
        <div class="btn-group">
          <button class="btn btn-sm btn-outline-primary" onclick='modalPaisEditar(${JSON.stringify(p)})'><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-outline-danger" onclick="borrarPais(${p.ID_Pais})"><i class="bi bi-trash"></i></button>
        </div>
      </td>`;
    tb.appendChild(tr);
  });
}

// ========== DEPARTAMENTOS ==========
let mdlDep;
function modalDepCrear(){
  $('#ttlDep').textContent='Nuevo departamento';
  $('#depId').value='';
  $('#NombreDep').value='';
  mdlDep.show();
}
function modalDepEditar(dep){
  $('#ttlDep').textContent='Editar departamento';
  $('#depId').value=dep.ID_Departamento;
  $('#NombreDep').value=dep.Nombre;
  $('#FK_ID_Pais').value = dep.FK_ID_Pais ?? dep.pais?.ID_Pais ?? '';
  mdlDep.show();
}
async function guardarDep(e){
  e.preventDefault();
  const id = $('#depId').value;
  const payload = {
    Nombre: $('#NombreDep').value.trim(),
    FK_ID_Pais: Number($('#FK_ID_Pais').value),
  };
  let url = `${API}/departamentos`, method='POST';
  if(id){ url = `${API}/departamentos/${id}`; method='PUT'; }
  const res = await fetch(url, { method, headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
  if(!res.ok){ window.showToast('Error al guardar departamento','danger'); return; }
  mdlDep.hide(); window.showToast('Departamento guardado','success'); await cargarDepartamentos(); await poblarSelects();
}
async function borrarDep(id){
  if(!confirm('¿Eliminar departamento?')) return;
  const res = await fetch(`${API}/departamentos/${id}`, { method:'DELETE' });
  if(!res.ok){ window.showToast('No se pudo eliminar','danger'); return; }
  window.showToast('Departamento eliminado','success'); await cargarDepartamentos(); await poblarSelects();
}
async function cargarDepartamentos(){
  const pais = $('#fDepPais').value;
  const s    = $('#searchDep').value?.trim() || '';
  const url = new URL(`${location.origin}${API}/departamentos`);
  if(pais) url.searchParams.set('pais', pais);
  if(s)    url.searchParams.set('search', s);
  const data = await (await fetch(url)).json();
  const tb = $('#tbDepartamentos'); tb.innerHTML='';
  data.forEach(d=>{
    const tr=document.createElement('tr');
    const paisNombre = d.pais?.Nombre ?? '-';
    tr.innerHTML=`
      <td>${d.Nombre}</td>
      <td>${paisNombre}</td>
      <td class="text-end">
        <div class="btn-group">
          <button class="btn btn-sm btn-outline-primary" onclick='modalDepEditar(${JSON.stringify(d)})'><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-outline-danger" onclick="borrarDep(${d.ID_Departamento})"><i class="bi bi-trash"></i></button>
        </div>
      </td>`;
    tb.appendChild(tr);
  });
}

// ========== MUNICIPIOS ==========
let mdlMun;
function modalMunCrear(){
  $('#ttlMun').textContent='Nuevo municipio';
  $('#munId').value='';
  $('#NombreMun').value='';
  mdlMun.show();
}
function modalMunEditar(m){
  $('#ttlMun').textContent='Editar municipio';
  $('#munId').value=m.ID_Municipio;
  $('#NombreMun').value=m.Nombre;
  $('#FK_ID_Departamento').value = m.FK_ID_Departamento ?? m.departamento?.ID_Departamento ?? '';
  mdlMun.show();
}
async function guardarMun(e){
  e.preventDefault();
  const id = $('#munId').value;
  const payload = {
    Nombre: $('#NombreMun').value.trim(),
    FK_ID_Departamento: Number($('#FK_ID_Departamento').value),
  };
  let url = `${API}/municipios`, method='POST';
  if(id){ url = `${API}/municipios/${id}`; method='PUT'; }
  const res = await fetch(url, { method, headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
  if(!res.ok){ window.showToast('Error al guardar municipio','danger'); return; }
  mdlMun.hide(); window.showToast('Municipio guardado','success'); await cargarMunicipios();
}
async function borrarMun(id){
  if(!confirm('¿Eliminar municipio?')) return;
  const res = await fetch(`${API}/municipios/${id}`, { method:'DELETE' });
  if(!res.ok){ window.showToast('No se pudo eliminar','danger'); return; }
  window.showToast('Municipio eliminado','success'); await cargarMunicipios();
}
async function cargarMunicipios(){
  const dep = $('#fMunDep').value;
  const s   = $('#searchMun').value?.trim() || '';
  const url = new URL(`${location.origin}${API}/municipios`);
  if(dep) url.searchParams.set('departamento', dep);
  if(s)   url.searchParams.set('search', s);
  const data = await (await fetch(url)).json();
  const tb = $('#tbMunicipios'); tb.innerHTML='';
  data.forEach(m=>{
    const tr=document.createElement('tr');
    const depNombre = m.departamento?.Nombre ?? '-';
    tr.innerHTML=`
      <td>${m.Nombre}</td>
      <td>${depNombre}</td>
      <td class="text-end">
        <div class="btn-group">
          <button class="btn btn-sm btn-outline-primary" onclick='modalMunEditar(${JSON.stringify(m)})'><i class="bi bi-pencil"></i></button>
          <button class="btn btn-sm btn-outline-danger" onclick="borrarMun(${m.ID_Municipio})"><i class="bi bi-trash"></i></button>
        </div>
      </td>`;
    tb.appendChild(tr);
  });
}

// ========== CARGA DE SELECTS ==========
async function poblarSelects(){
  const paises = await (await fetch(`${API}/paises`)).json();
  const deps   = await (await fetch(`${API}/departamentos`)).json();

  // filtros
  const fDepPais = $('#fDepPais'); fDepPais.innerHTML=''; fDepPais.append(createOpt('Todos los países',''));
  paises.forEach(p=> fDepPais.append(createOpt(`+${p.ID_Pais} - ${p.Nombre}`, p.ID_Pais)));

  const fMunDep = $('#fMunDep'); fMunDep.innerHTML=''; fMunDep.append(createOpt('Todos los departamentos',''));
  deps.forEach(d=> fMunDep.append(createOpt(`${d.Nombre}`, d.ID_Departamento)));

  // modales
  const FK_ID_Pais = $('#FK_ID_Pais'); FK_ID_Pais.innerHTML='';
  paises.forEach(p=> FK_ID_Pais.append(createOpt(`+${p.ID_Pais} - ${p.Nombre}`, p.ID_Pais)));

  const FK_ID_Departamento = $('#FK_ID_Departamento'); FK_ID_Departamento.innerHTML='';
  deps.forEach(d=> FK_ID_Departamento.append(createOpt(`${d.Nombre}`, d.ID_Departamento)));
}

// ========== INIT ==========
document.addEventListener('DOMContentLoaded', async ()=>{
  mdlPais = new bootstrap.Modal('#mdlPais');
  mdlDep  = new bootstrap.Modal('#mdlDep');
  mdlMun  = new bootstrap.Modal('#mdlMun');

  await poblarSelects();
  await cargarPaises();
  await cargarDepartamentos();
  await cargarMunicipios();
});
</script>
@endpush
