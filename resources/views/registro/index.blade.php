@extends('layouts.app')
@section('title','tasas')
@section('page-title','tasas')
@section('content')
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro | Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8fafc; }
    .card { border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.05); }
    .tab-pane { padding-top: 1rem; }
  </style>
</head>
<body>
<div class="container py-4">
  <h3 class="mb-3">Registros</h3>

  <ul class="nav nav-tabs" id="tabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pais" type="button">País</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-depto" type="button">Departamento</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-muni" type="button">Municipio</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-empresa" type="button">Empresa</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-usuario" type="button">Usuario</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tipo" type="button">Tipo de inversión</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-inversion" type="button">Inversión</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vinc" type="button">Vinculación</button></li>
  </ul>

  <div class="tab-content">
    <!-- PAIS -->
    <div class="tab-pane fade show active" id="tab-pais">
      <div class="card mt-3"><div class="card-body">
        <form id="frmPais" class="row g-3" onsubmit="submitPais(event)">
          <div class="col-md-4">
            <label class="form-label">ID País</label>
            <input type="text" name="ID_Pais" class="form-control" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Nombre</label>
            <input type="text" name="Nombre" class="form-control" required>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar país</button>
          </div>
        </form>
      </div></div>
    </div>

    <!-- DEPTO -->
    <div class="tab-pane fade" id="tab-depto">
      <div class="card mt-3"><div class="card-body">
        <form id="frmDepto" class="row g-3" onsubmit="submitDepto(event)">
          <div class="col-md-6">
            <label class="form-label">País</label>
            <select name="FK_ID_Pais" id="depPais" class="form-select" required></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nombre departamento</label>
            <input type="text" name="Nombre" class="form-control" required>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar departamento</button>
          </div>
        </form>
      </div></div>
    </div>

    <!-- MUNICIPIO -->
    <div class="tab-pane fade" id="tab-muni">
      <div class="card mt-3"><div class="card-body">
        <form id="frmMuni" class="row g-3" onsubmit="submitMuni(event)">
          <div class="col-md-6">
            <label class="form-label">País</label>
            <select id="muniPais" class="form-select" required></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Departamento</label>
            <select name="FK_ID_Departamento" id="muniDepto" class="form-select" required></select>
          </div>
          <div class="col-md-12">
            <label class="form-label">Nombre municipio</label>
            <input type="text" name="Nombre" class="form-control" required>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar municipio</button>
          </div>
        </form>
      </div></div>
    </div>

    <!-- EMPRESA (PROYECTO) -->
    <div class="tab-pane fade" id="tab-empresa">
      <div class="card mt-3"><div class="card-body">
        <form id="frmProyecto" class="row g-3" onsubmit="submitProyecto(event)" enctype="multipart/form-data">
          <div class="col-md-3">
            <label class="form-label">NIT (ID_Proyecto)</label>
            <input type="number" name="ID_Proyecto" class="form-control" required>
          </div>
          <div class="col-md-5">
            <label class="form-label">Nombre</label>
            <input type="text" name="Nombre" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Fecha</label>
            <input type="date" name="Fecha" class="form-control" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Descripción</label>
            <textarea name="Descripcion" class="form-control" rows="3" required></textarea>
          </div>
          <div class="col-md-4">
            <label class="form-label">Certificado (PDF/ZIP/JPG/PNG)</label>
            <input type="file" name="Certificado" class="form-control" accept=".pdf,.zip,.jpg,.jpeg,.png">
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar empresa</button>
          </div>
        </form>
      </div></div>
    </div>

    <!-- USUARIO -->
    <div class="tab-pane fade" id="tab-usuario">
      <div class="card mt-3"><div class="card-body">
        <form id="frmUsuario" class="row g-3" onsubmit="submitUsuario(event)">
          <div class="col-md-3"><label class="form-label">Cédula</label><input name="ID_Usuario" class="form-control" required></div>
          <div class="col-md-3"><label class="form-label">Nombre</label><input name="Nombre" class="form-control" required></div>
          <div class="col-md-3"><label class="form-label">Apellido</label><input name="Apellido" class="form-control" required></div>
          <div class="col-md-3"><label class="form-label">Teléfono</label><input name="Telefono" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Correo</label><input type="email" name="Correo" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Contraseña</label><input type="password" name="Contraseña" class="form-control" required></div>
          <div class="col-md-4"><label class="form-label">Fecha</label><input type="date" name="Fecha" class="form-control" required></div>
          <div class="col-md-6">
            <label class="form-label">País</label>
            <select id="userPais" class="form-select" required></select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Depto</label>
            <select id="userDepto" class="form-select" required></select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Municipio</label>
            <select name="FK_ID_Municipio" id="userMuni" class="form-select" required></select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Rol</label>
            <select name="FK_ID_Rol" class="form-select" required>
              <option value="2">Usuario</option>
              <option value="1">Admin</option>
            </select>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar usuario</button>
          </div>
        </form>
      </div></div>
    </div>

    <!-- TIPO INVERSIÓN -->
    <div class="tab-pane fade" id="tab-tipo">
      <div class="card mt-3"><div class="card-body">
        <form id="frmTipo" class="row g-3" onsubmit="submitTipo(event)">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input name="Nombre" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Descripción</label>
            <input name="Descripcion" class="form-control" required>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar tipo</button>
          </div>
        </form>
      </div></div>
    </div>

    <!-- INVERSIÓN -->
    <div class="tab-pane fade" id="tab-inversion">
      <div class="card mt-3"><div class="card-body">
        <form id="frmInversion" class="row g-3" onsubmit="submitInversion(event)" enctype="multipart/form-data">
          <div class="col-md-4">
            <label class="form-label">Usuario</label>
            <select name="FK_ID_Usuario" id="invUsuario" class="form-select" required></select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Proyecto</label>
            <select name="FK_ID_Proyecto" id="invProyecto" class="form-select" required></select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tipo</label>
            <select name="FK_ID_Tipo" id="invTipo" class="form-select" required></select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Nombre (alias)</label>
            <input name="Nombre" class="form-control" placeholder="ej. Aporte 1" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Monto</label>
            <input name="Monto" type="number" min="0" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <input name="Fecha" type="date" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Certificado (PDF)</label>
            <input name="CertificadoInversion" type="file" class="form-control" accept=".pdf" required>
          </div>
          <div class="col-md-12">
            <label class="form-label">Descripción</label>
            <textarea name="Descripcion" class="form-control" rows="3" required></textarea>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar inversión</button>
          </div>
        </form>
      </div></div>
    </div>

    <!-- VINCULACIÓN -->
    <div class="tab-pane fade" id="tab-vinc">
      <div class="card mt-3"><div class="card-body">
        <form id="frmVinc" class="row g-3" onsubmit="submitVinc(event)">
          <div class="col-md-6">
            <label class="form-label">Proyecto</label>
            <select id="vincProyecto" class="form-select" required></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Usuarios (múltiple)</label>
            <select id="vincUsuarios" class="form-select" multiple size="6" required></select>
          </div>
          <div class="col-12">
            <button class="btn btn-primary">Guardar vinculación</button>
          </div>
        </form>
      </div></div>
    </div>
  </div>

  <div id="alert" class="alert mt-3 d-none" role="alert"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API = '/api';
const alertBox = document.getElementById('alert');
const showAlert = (msg, type='success') => {
  alertBox.textContent = msg;
  alertBox.className = `alert alert-${type} mt-3`;
  alertBox.classList.remove('d-none');
  setTimeout(()=> alertBox.classList.add('d-none'), 4000);
};

// ===== Helpers =====
async function getJSON(url){ const r = await fetch(url); if(!r.ok) throw new Error(url); return r.json(); }
function toFormData(form){ const fd = new FormData(form); return fd; }

// ===== Catálogos =====
async function loadPaises(select){
  const data = await getJSON(`${API}/paises?per_page=1000`);
  select.innerHTML = '<option value="">Seleccione...</option>';
  (data.data ?? data).forEach(p => select.add(new Option(`${p.ID_Pais} - ${p.Nombre}`, p.ID_Pais)));
}
async function loadDeptos(select, paisId){
  const data = await getJSON(`${API}/departamentos?FK_ID_Pais=${paisId}&per_page=1000`);
  select.innerHTML = '<option value="">Seleccione...</option>';
  (data.data ?? data).forEach(d => select.add(new Option(d.Nombre, d.ID_Departamento)));
}
async function loadMunicipios(select, deptoId){
  const data = await getJSON(`${API}/municipios?FK_ID_Departamento=${deptoId}&per_page=1000`);
  select.innerHTML = '<option value="">Seleccione...</option>';
  (data.data ?? data).forEach(m => select.add(new Option(m.Nombre, m.ID_Municipio)));
}
async function loadUsuarios(select){
  const data = await getJSON(`${API}/usuarios?per_page=1000`);
  select.innerHTML = '<option value="">Seleccione...</option>';
  (data.data ?? data).forEach(u => {
    const name = `${u.ID_Usuario} - ${u.Nombre ?? ''} ${u.Apellido ?? ''}`.trim();
    select.add(new Option(name, u.ID_Usuario));
  });
}
async function loadUsuariosMulti(select){
  const data = await getJSON(`${API}/usuarios?per_page=1000`);
  select.innerHTML = '';
  (data.data ?? data)
    // Si quieres excluir admin (FK_ID_Rol == 1) y tu API no filtra:
    // .filter(u => (u.FK_ID_Rol ?? 0) != 1)
    .forEach(u => {
      const name = `${u.ID_Usuario} - ${u.Nombre ?? ''} ${u.Apellido ?? ''}`.trim();
      select.add(new Option(name, u.ID_Usuario));
    });
}
async function loadProyectos(select){
  const data = await getJSON(`${API}/proyectos?per_page=1000`);
  select.innerHTML = '<option value="">Seleccione...</option>';
  (data.data ?? data).forEach(p => select.add(new Option(`${p.ID_Proyecto} - ${p.Nombre}`, p.ID_Proyecto)));
}
async function loadTipos(select){
  const data = await getJSON(`${API}/tipos-inversion?per_page=1000`);
  select.innerHTML = '<option value="">Seleccione...</option>';
  (data.data ?? data).forEach(t => select.add(new Option(`${t.ID_TIPO ?? t.ID ?? ''} - ${t.Nombre}`, t.ID_TIPO ?? t.ID)));
}

// ===== Submits =====
async function submitPais(e){
  e.preventDefault();
  const data = Object.fromEntries(new FormData(e.target).entries());
  const res = await fetch(`${API}/paises`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)});
  if(!res.ok){ showAlert('No se pudo guardar el país','danger'); return; }
  showAlert('País guardado.');
  e.target.reset();
}
async function submitDepto(e){
  e.preventDefault();
  const data = Object.fromEntries(new FormData(e.target).entries());
  const res = await fetch(`${API}/departamentos`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)});
  if(!res.ok){ showAlert('No se pudo guardar el departamento','danger'); return; }
  showAlert('Departamento guardado.');
  e.target.reset();
}
async function submitMuni(e){
  e.preventDefault();
  const data = Object.fromEntries(new FormData(e.target).entries());
  const res = await fetch(`${API}/municipios`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)});
  if(!res.ok){ showAlert('No se pudo guardar el municipio','danger'); return; }
  showAlert('Municipio guardado.');
  e.target.reset();
}
async function submitProyecto(e){
  e.preventDefault();
  const fd = toFormData(e.target); // incluye archivo
  const res = await fetch(`${API}/proyectos`, { method:'POST', body: fd });
  if(!res.ok){ showAlert('No se pudo guardar la empresa','danger'); return; }
  showAlert('Empresa guardada.');
  e.target.reset();
}
async function submitUsuario(e){
  e.preventDefault();
  const data = Object.fromEntries(new FormData(e.target).entries());
  const res = await fetch(`${API}/usuarios`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)});
  if(!res.ok){ showAlert('No se pudo guardar el usuario','danger'); return; }
  showAlert('Usuario guardado.');
  e.target.reset();
}
async function submitTipo(e){
  e.preventDefault();
  const data = Object.fromEntries(new FormData(e.target).entries());
  const res = await fetch(`${API}/tipos-inversion`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)});
  if(!res.ok){ showAlert('No se pudo guardar el tipo','danger'); return; }
  showAlert('Tipo de inversión guardado.');
  e.target.reset();
}
async function submitInversion(e){
  e.preventDefault();
  const fd = toFormData(e.target); // incluye PDF
  const res = await fetch(`${API}/inversiones`, { method:'POST', body: fd });
  if(!res.ok){ showAlert('No se pudo guardar la inversión','danger'); return; }
  showAlert('Inversión guardada.');
  e.target.reset();
}
async function submitVinc(e){
  e.preventDefault();
  const proyecto = document.getElementById('vincProyecto').value;
  const usuarios = Array.from(document.getElementById('vincUsuarios').selectedOptions).map(o => o.value);
  if(!proyecto || usuarios.length===0){ showAlert('Selecciona proyecto y al menos un usuario','warning'); return; }
  const res = await fetch(`${API}/vinculaciones`, {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({ proyecto: parseInt(proyecto,10), usuarios: usuarios.map(v => parseInt(v,10)) })
  });
  if(!res.ok){ showAlert('No se pudo guardar la vinculación','danger'); return; }
  showAlert('Vinculación guardada.');
  e.target.reset();
}

// ===== Cascadas de selects =====
document.addEventListener('DOMContentLoaded', async () => {
  // Países → deptos (tab depto)
  await loadPaises(document.getElementById('depPais'));

  // Países → deptos → municipios (tab muni + usuario)
  const muniPais = document.getElementById('muniPais');
  const muniDepto = document.getElementById('muniDepto');
  await loadPaises(muniPais);
  muniPais.addEventListener('change', async e => {
    muniDepto.innerHTML = '<option value="">Seleccione...</option>';
    if(e.target.value) await loadDeptos(muniDepto, e.target.value);
  });

  const userPais = document.getElementById('userPais');
  const userDepto = document.getElementById('userDepto');
  const userMuni = document.getElementById('userMuni');
  await loadPaises(userPais);
  userPais.addEventListener('change', async e => {
    userDepto.innerHTML = '<option value="">Seleccione...</option>';
    userMuni.innerHTML = '<option value="">Seleccione...</option>';
    if(e.target.value) await loadDeptos(userDepto, e.target.value);
  });
  userDepto.addEventListener('change', async e => {
    userMuni.innerHTML = '<option value="">Seleccione...</option>';
    if(e.target.value) await loadMunicipios(userMuni, e.target.value);
  });

  // Proyectos, Usuarios, Tipos (para inversión y vinculación)
  await loadUsuarios(document.getElementById('invUsuario'));
  await loadProyectos(document.getElementById('invProyecto'));
  await loadTipos(document.getElementById('invTipo'));

  await loadProyectos(document.getElementById('vincProyecto'));
  await loadUsuariosMulti(document.getElementById('vincUsuarios'));
});
</script>
</body>
</html>
@endsection('content')