@extends('layouts.app')
@section('title','simulacion')
@section('page-title','simulacion')
@section('content')
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Simulación</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body { background: #f8fafc; font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial; }
    .card { border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
    .chart { min-height: 320px; }
    .table thead th { white-space: nowrap; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex flex-wrap align-items-end gap-3 mb-4">
    <div>
      <h3 class="mb-1">Simulación</h3>
      <div class="text-secondary">Selecciona una empresa para calcular aportes y participaciones (ajustadas por tasa).</div>
    </div>
    <div class="ms-auto">
      <label class="form-label mb-1">Empresa</label>
      <select id="selProyecto" class="form-select">
        <option value="">Seleccione...</option>
      </select>
    </div>
    <div>
      <label class="form-label mb-1">Año (series mensuales)</label>
      <select id="selYear" class="form-select"></select>
    </div>
  </div>

  <!-- Tarjetas -->
  <div class="row g-3 mb-3">
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body">
        <div class="text-secondary">Usuarios vinculados</div>
        <div class="display-6" id="cardUsuarios">0</div>
      </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body">
        <div class="text-secondary">Aportes capital (VF)</div>
        <div class="h4 mb-0" id="cardCapital">$ 0</div>
      </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body">
        <div class="text-secondary">Aportes industria (VF)</div>
        <div class="h4 mb-0" id="cardIndustria">$ 0</div>
      </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body">
        <div class="text-secondary">Tasa ajustada</div>
        <div class="h4 mb-0"><span id="cardTasa">0</span>%</div>
      </div></div>
    </div>
  </div>

  <!-- % min / avg / max -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card text-bg-light"><div class="card-body">
        <div class="text-secondary">Participación mínima</div>
        <div class="h4 mb-0" id="cardMin">0%</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card text-bg-light"><div class="card-body">
        <div class="text-secondary">Promedio participación</div>
        <div class="h4 mb-0" id="cardAvg">0%</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card text-bg-light"><div class="card-body">
        <div class="text-secondary">Participación máxima</div>
        <div class="h4 mb-0" id="cardMax">0%</div>
      </div></div>
    </div>
  </div>

  <!-- Gráficas -->
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card"><div class="card-body">
        <h6 class="mb-3">Aportes (Capital vs Industria) – Donut</h6>
        <div id="chartDonut" class="chart"></div>
      </div></div>
    </div>
    <div class="col-lg-6">
      <div class="card"><div class="card-body">
        <h6 class="mb-3">% Participación por socio (Top → Bottom)</h6>
        <div id="chartBarrasPct" class="chart"></div>
      </div></div>
    </div>
    <div class="col-12">
      <div class="card"><div class="card-body">
        <h6 class="mb-3">Inversiones por mes (Capital vs Industria)</h6>
        <div id="chartMensual" class="chart"></div>
      </div></div>
    </div>
  </div>

  <!-- Tabla -->
  <div class="card mt-4">
    <div class="card-body">
      <h6 class="mb-3">Resumen por socio</h6>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>Socio</th>
              <th class="text-end">Aportes Capital (VF)</th>
              <th class="text-end">Aportes Industria (VF)</th>
              <th class="text-end">Total (VF)</th>
              <th class="text-center">% Participación</th>
            </tr>
          </thead>
          <tbody id="tbodySocios"></tbody>
          <tfoot>
            <tr>
              <th>TOTALES</th>
              <th class="text-end" id="tCap">$ 0</th>
              <th class="text-end" id="tInd">$ 0</th>
              <th class="text-end" id="tTot">$ 0</th>
              <th class="text-center">100%</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
const API = '/api';
let donut, barrasPct, mensual;

function money(n){ return n.toLocaleString('es-CO', {style:'currency', currency:'COP', maximumFractionDigits:0}); }
function pct(n){ return (n ?? 0).toLocaleString('es-CO', {maximumFractionDigits:2}) + '%'; }

async function loadProyectos() {
  const sel = document.getElementById('selProyecto');
  sel.innerHTML = '<option value="">Seleccione...</option>';
  const res = await fetch(`${API}/catalogos/proyectos-no-liquidados`);
  const items = await res.json();
  for (const p of items) {
    const opt = document.createElement('option');
    opt.value = p.ID_Proyecto;
    opt.textContent = p.Nombre;
    sel.appendChild(opt);
  }
}

function loadYears() {
  const y = document.getElementById('selYear');
  const cur = new Date().getFullYear();
  for (let a = cur; a >= cur - 5; a--) {
    const opt = document.createElement('option');
    opt.value = a; opt.textContent = a;
    y.appendChild(opt);
  }
  y.value = cur;
}

async function loadResumen() {
  const proyecto_id = document.getElementById('selProyecto').value;
  const year = document.getElementById('selYear').value;
  if (!proyecto_id) return;

  const url = new URL(`${API}/simulacion/resumen`, window.location.origin);
  url.searchParams.set('proyecto_id', proyecto_id);
  url.searchParams.set('year', year);

  const res = await fetch(url);
  if (!res.ok) { alert('No se pudo calcular la simulación'); return; }
  const data = await res.json();

  // Tarjetas
  document.getElementById('cardUsuarios').textContent = data.cards.cantidad_usuarios ?? 0;
  document.getElementById('cardCapital').textContent  = money(data.cards.valor_aportes_capital ?? 0);
  document.getElementById('cardIndustria').textContent= money(data.cards.valor_aportes_industria ?? 0);
  document.getElementById('cardTasa').textContent     = (data.tasa_ajustada ?? 0);

  document.getElementById('cardMin').textContent = pct(data.cards.participacion_minima);
  document.getElementById('cardAvg').textContent = pct(data.cards.promedio_participacion);
  document.getElementById('cardMax').textContent = pct(data.cards.participacion_maxima);

  // Donut
  const dSeries = data.series.aportes_donut.data;
  const dLabels = data.series.aportes_donut.labels;
  const donutOpt = {
    chart: { type: 'donut', height: 320 },
    series: dSeries,
    labels: dLabels,
    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
    tooltip: { y: { formatter: (val) => money(val) } },
    legend: { position: 'bottom' }
  };
  if (donut) donut.destroy();
  donut = new ApexCharts(document.querySelector('#chartDonut'), donutOpt);
  donut.render();

  // Barras % por socio
  const b = data.series.usuarios_porcentaje;
  const barrasOpt = {
    chart: { type: 'bar', height: 320 },
    series: [{ name: '%', data: b.data }],
    xaxis: { categories: b.labels },
    plotOptions: { bar: { borderRadius: 6, horizontal: true } },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: (v) => v.toFixed(2) + '%' } }
  };
  if (barrasPct) barrasPct.destroy();
  barrasPct = new ApexCharts(document.querySelector('#chartBarrasPct'), barrasOpt);
  barrasPct.render();

  // Mensual capital vs industria
  const m = data.series.mensual;
  const mensualOpt = {
    chart: { type: 'area', height: 320 },
    series: [
      { name: 'Capital', data: m.capital },
      { name: 'Industria', data: m.industria }
    ],
    xaxis: { categories: m.labels },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    tooltip: { y: { formatter: (v) => money(v) } }
  };
  if (mensual) mensual.destroy();
  mensual = new ApexCharts(document.querySelector('#chartMensual'), mensualOpt);
  mensual.render();

  // Tabla
  const tbody = document.getElementById('tbodySocios');
  tbody.innerHTML = '';
  let sumCap = 0, sumInd = 0, sumTot = 0;
  for (const row of data.tabla) {
    sumCap += row.Capital; sumInd += row.Industria; sumTot += row.Total;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${row.Nombre}</td>
      <td class="text-end">${money(row.Capital)}</td>
      <td class="text-end">${money(row.Industria)}</td>
      <td class="text-end">${money(row.Total)}</td>
      <td class="text-center">${row.Porcentaje.toFixed(2)}%</td>
    `;
    tbody.appendChild(tr);
  }
  document.getElementById('tCap').textContent = money(sumCap);
  document.getElementById('tInd').textContent = money(sumInd);
  document.getElementById('tTot').textContent = money(sumTot);
}

document.addEventListener('DOMContentLoaded', async () => {
  loadYears();
  await loadProyectos();

  document.getElementById('selProyecto').addEventListener('change', loadResumen);
  document.getElementById('selYear').addEventListener('change', loadResumen);
});
</script>
</body>
</html>
@endsection('content')