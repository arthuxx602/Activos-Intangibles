@extends('layouts.app')
@section('title','liquidar-proyecto')
@section('page-title','liquidar-proyecto')
@section('content')
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Liquidar Proyecto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8fafc; }
    .card { border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.05); }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Liquidar proyecto</h3>
  </div>

  <div class="card">
    <div class="card-body">
      <form id="frm" class="row g-3" onsubmit="enviar(event)" enctype="multipart/form-data">
        <div class="col-md-6">
          <label class="form-label">Proyecto (no liquidado)</label>
          <select id="proyecto" class="form-select" required></select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Acta de liquidación (PDF/ZIP/JPG/PNG)</label>
          <input id="documento_L" name="documento_L" type="file" class="form-control" accept=".pdf,.zip,.jpg,.jpeg,.png" required>
        </div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary" type="submit">Liquidar</button>
          <button class="btn btn-outline-secondary" type="button" onclick="frm.reset()">Limpiar</button>
        </div>
      </form>

      <div id="alert" class="alert mt-3 d-none" role="alert"></div>
    </div>
  </div>

  <div class="card mt-3">
    <div class="card-body">
      <h5>Proyectos liquidados recientemente</h5>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead><tr><th>ID</th><th>Nombre</th><th>Estado</th><th>Acta</th></tr></thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
const API = '/api';
const frm = document.getElementById('frm');
const alerta = document.getElementById('alert');

function showAlert(msg, type='success'){
  alerta.textContent = msg;
  alerta.className = `alert alert-${type} mt-3`;
  alerta.classList.remove('d-none');
  setTimeout(()=> alerta.classList.add('d-none'), 4000);
}

async function cargarProyectosNoLiquidados(){
  // Debes tener este endpoint en tu ConsultaController
  // GET /api/catalogos/proyectos-no-liquidados  => [{ID_Proyecto, Nombre}]
  const res = await fetch(`${API}/catalogos/proyectos-no-liquidados`);
  if(!res.ok){ showAlert('No se pudieron cargar proyectos.', 'danger'); return; }
  const data = await res.json();
  const sel = document.getElementById('proyecto');
  sel.innerHTML = `<option value="">Seleccione...</option>`;
  data.forEach(p=>{
    const opt = new Option(`${p.ID_Proyecto} - ${p.Nombre}`, p.ID_Proyecto);
    sel.add(opt);
  });
}

async function cargarLiquidados(){
  // Reutilizamos /api/proyectos?search&per_page=…
  const res = await fetch(`${API}/proyectos?per_page=10&search=&solo_no_liquidados=0`);
  const json = await res.json();
  const rows = (json.data ?? json).filter(p => (p.liquidado ?? 0) == 1).slice(0,10);
  const tb = document.getElementById('tbody');
  tb.innerHTML = '';
  rows.forEach(p=>{
    const tr = document.createElement('tr');
    const descarga = p.Certificado_L
      ? `<a class="btn btn-sm btn-outline-secondary" target="_blank" href="${API}/descargas/proyectos-liquidacion/${p.ID_Proyecto}">Descargar acta</a>`
      : '—';
    tr.innerHTML = `
      <td>${p.ID_Proyecto}</td>
      <td>${p.Nombre}</td>
      <td>${(p.liquidado==1)?'Liquidado':'—'}</td>
      <td>${descarga}</td>
    `;
    tb.appendChild(tr);
  });
}

async function enviar(e){
  e.preventDefault();
  const proyecto = document.getElementById('proyecto').value;
  const fileInput = document.getElementById('documento_L');
  if(!proyecto){ showAlert('Selecciona un proyecto','warning'); return; }
  if(!fileInput.files.length){ showAlert('Adjunta el acta de liquidación','warning'); return; }

  const fd = new FormData();
  fd.append('documento_L', fileInput.files[0]);

  const res = await fetch(`${API}/proyectos/${proyecto}/liquidar`, { method: 'POST', body: fd });
  const data = await res.json().catch(()=> ({}));
  if(!res.ok){
    showAlert(data.message || 'No se pudo liquidar.', 'danger');
    return;
  }

  showAlert('Proyecto liquidado correctamente.');
  frm.reset();
  await cargarProyectosNoLiquidados();
  await cargarLiquidados();
}

document.addEventListener('DOMContentLoaded', async ()=>{
  await cargarProyectosNoLiquidados();
  await cargarLiquidados();
});
</script>
</body>
</html>
@endsection('content')