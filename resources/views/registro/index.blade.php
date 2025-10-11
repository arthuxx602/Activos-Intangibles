@extends('layouts.app')

@section('title','Registro')
@section('page-title','Registro de parámetros')

@section('content')
<div class="card">
  <div class="card-body">
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pais" type="button">País</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-depto" type="button">Departamento</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-mpio" type="button">Municipio</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tipo" type="button">Tipos de inversión</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-proy" type="button">Proyecto</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vinc" type="button">Vincular usuarios ⇄ proyecto</button></li>
    </ul>

    <div class="tab-content pt-3">
      {{-- PAÍS --}}
      <div class="tab-pane fade show active" id="tab-pais" role="tabpanel">
        <form id="formPais" class="row g-3" onsubmit="guardarPais(event)">
          <div class="col-md-4">
            <label class="form-label">ID País</label>
            <input id="pais_id" name="ID_Pais" class="form-control" placeholder="CO">
          </div>
          <div class="col-md-8">
            <label class="form-label">Nombre</label>
            <input id="pais_nombre" name="Nombre" class="form-control" placeholder="Colombia">
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar país</button>
          </div>
        </form>
      </div>

      {{-- DEPARTAMENTO --}}
      <div class="tab-pane fade" id="tab-depto" role="tabpanel">
        <form id="formDepto" class="row g-3" onsubmit="guardarDepto(event)">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input id="depto_nombre" name="Nombre" class="form-control" placeholder="Antioquia" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">País</label>
            <select id="depto_pais" name="FK_ID_Pais" class="form-select" required></select>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar departamento</button>
          </div>
        </form>
      </div>

      {{-- MUNICIPIO --}}
      <div class="tab-pane fade" id="tab-mpio" role="tabpanel">
        <form id="formMpio" class="row g-3" onsubmit="guardarMpio(event)">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input id="mpio_nombre" name="Nombre" class="form-control" placeholder="Medellín" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Departamento</label>
            <select id="mpio_depto" name="FK_ID_Departamento" class="form-select" required></select>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar municipio</button>
          </div>
        </form>
      </div>

      {{-- TIPO DE INVERSIÓN --}}
      <div class="tab-pane fade" id="tab-tipo" role="tabpanel">
        <form id="formTipo" class="row g-3" onsubmit="guardarTipo(event)">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input id="tipo_nombre" name="Nombre" class="form-control" placeholder="Capital / Especie / Industria" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Descripción</label>
            <input id="tipo_desc" name="Descripcion" class="form-control">
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar tipo</button>
          </div>
        </form>
      </div>

      {{-- PROYECTO --}}
      <div class="tab-pane fade" id="tab-proy" role="tabpanel">
        <form id="formProy" class="row g-3" onsubmit="guardarProyecto(event)" enctype="multipart/form-data">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input id="proy_nombre" name="Nombre" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <input id="proy_fecha" name="Fecha" type="date" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Certificado (opcional)</label>
            <input id="proy_cert" name="Certificado" type="file" class="form-control" accept=".pdf,.zip,.jpg,.jpeg,.png">
          </div>
          <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea id="proy_desc" name="Descripcion" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar proyecto</button>
          </div>
        </form>
      </div>

      {{-- VINCULAR USUARIOS A PROYECTO --}}
      <div class="tab-pane fade" id="tab-vinc" role="tabpanel">
        <form id="formVinc" class="row g-3" onsubmit="guardarVinc(event)">
          <div class="col-md-6">
            <label class="form-label">Proyecto</label>
            <select id="vinc_proy" class="form-select" required></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Usuarios (Ctrl/⌘ para multi-selección)</label>
            <select id="vinc_users" class="form-select" size="6" multiple required></select>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Vincular</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const API='/api';
const $=s=>document.querySelector(s);

async function loadCatalogos(){
  // Paises
  const paises = await (await fetch(`${API}/paises?per_page=1000`)).json();
  const listPaises = paises.data ?? paises;
  depto_pais.innerHTML = `<option value="">Seleccione...</option>`;
  listPaises.forEach(p=> depto_pais.add(new Option(`${p.ID_Pais} - ${p.Nombre}`, p.ID_Pais)));

  // Departamentos
  const dpt = await (await fetch(`${API}/departamentos?per_page=1000`)).json();
  const listDpt = dpt.data ?? dpt;
  mpio_depto.innerHTML = `<option value="">Seleccione...</option>`;
  listDpt.forEach(d=> mpio_depto.add(new Option(`${d.ID_Departamento} - ${d.Nombre}`, d.ID_Departamento)));

  // Proyectos
  const pr = await (await fetch(`${API}/proyectos?per_page=1000`)).json();
  const listPr = pr.data ?? pr;
  vinc_proy.innerHTML = `<option value="">Seleccione...</option>`;
  listPr.forEach(p=> vinc_proy.add(new Option(`${p.ID_Proyecto} - ${p.Nombre}`, p.ID_Proyecto)));

  // Usuarios
  const us = await (await fetch(`${API}/catalogos/usuarios-para-vincular`)).json();
  vinc_users.innerHTML = '';
  (us||[]).forEach(u=> vinc_users.add(new Option(`${u.ID_Usuario} - ${u.Nombre} ${u.Apellido}`, u.ID_Usuario)));
}

// ====== Submit handlers ======
async function guardarPais(e){
  e.preventDefault();
  const body = new URLSearchParams({ID_Pais:pais_id.value, Nombre:pais_nombre.value});
  const r = await fetch(`${API}/paises`, {method:'POST', body});
  r.ok ? showToast('País guardado','success') : showToast('No se pudo guardar país','danger');
  if(r.ok) loadCatalogos();
}
async function guardarDepto(e){
  e.preventDefault();
  const body = new URLSearchParams({Nombre:depto_nombre.value, FK_ID_Pais:depto_pais.value});
  const r = await fetch(`${API}/departamentos`, {method:'POST', body});
  r.ok ? showToast('Departamento guardado','success') : showToast('No se pudo guardar departamento','danger');
  if(r.ok) loadCatalogos();
}
async function guardarMpio(e){
  e.preventDefault();
  const body = new URLSearchParams({Nombre:mpio_nombre.value, FK_ID_Departamento:mpio_depto.value});
  const r = await fetch(`${API}/municipios`, {method:'POST', body});
  r.ok ? showToast('Municipio guardado','success') : showToast('No se pudo guardar municipio','danger');
}
async function guardarTipo(e){
  e.preventDefault();
  const body = new URLSearchParams({Nombre:tipo_nombre.value, Descripcion:tipo_desc.value});
  const r = await fetch(`${API}/tipos-inversion`, {method:'POST', body});
  r.ok ? showToast('Tipo guardado','success') : showToast('No se pudo guardar tipo','danger');
}
async function guardarProyecto(e){
  e.preventDefault();
  const fd = new FormData(formProy);
  const r = await fetch(`${API}/proyectos`, {method:'POST', body:fd});
  r.ok ? showToast('Proyecto guardado','success') : showToast('No se pudo guardar proyecto','danger');
  if(r.ok){ formProy.reset(); loadCatalogos(); }
}
async function guardarVinc(e){
  e.preventDefault();
  const usuarios = Array.from(vinc_users.selectedOptions).map(o=>o.value);
  if(!usuarios.length) return showToast('Selecciona uno o más usuarios','warning');

  const body = JSON.stringify({proyecto: parseInt(vinc_proy.value), usuarios: usuarios.map(Number)});
  const r = await fetch(`${API}/vinculaciones/proyecto-usuarios`, {
    method:'POST',
    headers:{'Content-Type':'application/json','Accept':'application/json'},
    body
  });
  const j = await r.json().catch(()=>({}));
  if(!r.ok) return showToast(j.message || 'No se pudo vincular', 'danger');
  showToast('Usuarios vinculados al proyecto','success');
}

document.addEventListener('DOMContentLoaded', loadCatalogos);
</script>
@endpush
