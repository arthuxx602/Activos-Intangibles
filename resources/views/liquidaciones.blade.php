<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Liquidación de Proyecto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background:#f8fafc; }
    .card { border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.05); }
    .small-muted { font-size:.85rem; color:#6c757d; }
  </style>
</head>
<body>
<div class="container py-4">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Liquidación</h3>
  </div>

  <!-- FILTROS -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-sm-6">
          <label class="form-label">Proyecto</label>
          <select id="fProyecto" class="form-select"></select>
        </div>
        <div class="col-sm-3">
          <label class="form-label">Fecha de corte</label>
          <input id="fCorte" type="date" class="form-control">
        </div>
        <div class="col-sm-3 d-flex gap-2">
          <button class="btn btn-primary w-100" onclick="cargar()">Calcular</button>
          <button id="btnLiquidar" class="btn btn-danger w-100" onclick="liquidar()" disabled>
            Liquidar proyecto
          </button>
        </div>
      </div>
      <div class="small-muted mt-2">Se usa la última tasa registrada para capitalización compuesta diaria.</div>
    </div>
  </div>

  <!-- RESUMEN -->
  <div id="resumen" class="row g-3 d-none">
    <div class="col-sm-6 col-lg-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-secondary">Tasa usada</div>
        <div class="fs-3 fw-bold"><span id="rTasa">0</span>%</div>
        <div class="small-muted">Fuente tasa Id: <span id="rTasaId">—</span></div>
      </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-secondary">Vida del proyecto</div>
        <div class="fs-3 fw-bold"><span id="rVida">0</span> días</div>
        <div class="small-muted">Fecha corte: <span id="rCorte">—</span></div>
      </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-secondary">Aportes (capital)</div>
        <div class="fw-semibold">Dinero: <span id="rDinero">$ 0</span></div>
        <div class="fw-semibold">Especie: <span id="rEspecie">$ 0</span></div>
      </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-secondary">Industria + Total</div>
        <div class="fw-semibold">Industria: <span id="rIndustria">$ 0</span></div>
        <div class="fs-4 fw-bold text-primary">Total: <span id="rTotal">$ 0</span></div>
      </div></div>
    </div>
  </div>

  <!-- TABLA USUARIOS -->
  <div id="bloqueTabla" class="card mt-3 d-none">
    <div class="card-body">
      <h5 class="mb-3">Distribución por usuario</h5>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Usuario</th>
              <th class="text-end">Dinero (aj.)</th>
              <th class="text-end">Especie (aj.)</th>
              <th class="text-end">Industria (aj.)</th>
              <th class="text-end">Total (aj.)</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
      <div class="small-muted">*Valores ajustados al <span id="lblCorte2">—</span>.</div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API = '/api';
const fmt = new Intl.NumberFormat('es-CO', { style:'currency', currency:'COP', maximumFractionDigits: 0 });
let proyectoActual = null;
let resumenActual = null;

async function cargarProyectos() {
  const res = await fetch(`${API}/proyectos?per_page=1000`);
  const data = await res.json();
  const list = data.data ?? data;
  const sel = document.getElementById('fProyecto');
  sel.innerHTML = `<option value="">Seleccione...</option>`;
  list.forEach(p => {
    const opt = new Option(`${p.ID_Proyecto} - ${p.Nombre}`, p.ID_Proyecto);
    sel.add(opt);
  });
}

function setHoy() {
  const inp = document.getElementById('fCorte');
  const hoy = new Date().toISOString().slice(0,10);
  inp.value = hoy;
}

function pintarResumen(json) {
  document.getElementById('resumen').classList.remove('d-none');
  document.getElementById('bloqueTabla').classList.remove('d-none');

  document.getElementById('rTasa').textContent = json.tasa.valor ?? 0;
  document.getElementById('rTasaId').textContent = json.tasa.fuente ?? '—';
  document.getElementById('rVida').textContent = json.vida_proyecto_dias ?? 0;
  document.getElementById('rCorte').textContent = json.fecha_corte ?? '—';

  document.getElementById('rDinero').textContent    = fmt.format(json.totales.dinero ?? 0);
  document.getElementById('rEspecie').textContent   = fmt.format(json.totales.especie ?? 0);
  document.getElementById('rIndustria').textContent = fmt.format(json.totales.industria ?? 0);
  document.getElementById('rTotal').textContent     = fmt.format(json.totales.total ?? 0);

  document.getElementById('lblCorte2').textContent = json.fecha_corte ?? '—';

  // Tabla usuarios
  const tbody = document.getElementById('tbody');
  tbody.innerHTML = '';
  (json.usuarios || []).forEach(u => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${u.nombre || u.usuario_id}</td>
      <td class="text-end">${fmt.format(u.dinero || 0)}</td>
      <td class="text-end">${fmt.format(u.especie || 0)}</td>
      <td class="text-end">${fmt.format(u.industria || 0)}</td>
      <td class="text-end fw-semibold">${fmt.format(u.total || 0)}</td>
    `;
    tbody.appendChild(tr);
  });

  // Habilitar botón liquidar si el proyecto no está marcado como liquidado
  const btn = document.getElementById('btnLiquidar');
  btn.disabled = (json.proyecto.liquidado == 1);
}

async function cargar() {
  const proyecto = document.getElementById('fProyecto').value;
  const corte    = document.getElementById('fCorte').value;
  if (!proyecto) { alert('Selecciona un proyecto'); return; }

  const url = new URL(`${API}/liquidaciones`, window.location.origin);
  url.searchParams.set('proyecto', proyecto);
  if (corte) url.searchParams.set('fecha_corte', corte);

  const res = await fetch(url);
  if (!res.ok) {
    alert('No fue posible calcular la liquidación.'); 
    return;
  }
  const json = await res.json();
  proyectoActual = json.proyecto?.id || proyecto;
  resumenActual = json;
  pintarResumen(json);
}

async function liquidar() {
  if (!proyectoActual) { alert('Calcula primero la liquidación.'); return; }
  if (!confirm('¿Confirmas liquidar el proyecto? Esta acción no se puede deshacer.')) return;

  const res = await fetch(`${API}/proyectos/${proyectoActual}/liquidar`, { method: 'POST' });
  if (!res.ok) {
    const err = await res.json().catch(()=> ({}));
    alert('No fue posible liquidar: ' + (err.message || res.status));
    return;
  }
  alert('Proyecto liquidado correctamente.');
  document.getElementById('btnLiquidar').disabled = true;
}

// init
document.addEventListener('DOMContentLoaded', async () => {
  await cargarProyectos();
  setHoy();
});
</script>
</body>
</html>
