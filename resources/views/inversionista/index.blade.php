@extends('layouts.app')

@section('title', 'Inversionista | Panel')
@section('page-title', 'Panel del Inversionista')

@section('head')
<style>
  .card { border-radius:16px; box-shadow:0 2px 8px rgba(0,0,0,.05); }
</style>
@endsection

@section('content')
  {{-- Encabezado de bienvenida --}}
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-center">
        <div class="col-md-4 text-center">
          <img src="https://dummyimage.com/480x200/eff2f7/6c757d&text=Bienvenido" class="img-fluid rounded" alt="">
        </div>
        <div class="col-md-5">
          <h4 class="mb-1">¡Bienvenido a la empresa: <span id="proyectoNombre">{{ $nombreProyectoSesion ?? '—' }}</span>!</h4>
          <div class="fs-5 text-primary fw-semibold">{{ ($nombre ?? 'Usuario').' '.($apellido ?? '') }}</div>
          <p class="text-secondary mb-0">Gestionamos tus inversiones y te mostramos el rendimiento en tiempo real.</p>
        </div>
        <div class="col-md-3">
          <h6 class="text-muted mb-1">Hora actual en Colombia</h6>
          <iframe src="https://www.zeitverschiebung.net/clock-widget-iframe-v2?language=es&size=medium&timezone=America%2FBogota" width="100%" height="115" frameborder="0" seamless></iframe>
        </div>
      </div>
    </div>
  </div>

  {{-- Selector de proyecto (opcional visible) --}}
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Proyecto</label>
          <select id="proyecto" class="form-select">
            <option value="">Auto (vinculado por usuario)</option>
            @foreach($proyectos as $p)
              <option value="{{ $p->ID_Proyecto }}" @selected($proyectoIdSesion == $p->ID_Proyecto)>{{ $p->ID_Proyecto }} - {{ $p->Nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <button id="btnAplicar" class="btn btn-primary w-100">
            <i class="bi bi-arrow-repeat me-1"></i> Actualizar
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- KPIs --}}
  <div class="row g-3 mb-3">
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="fs-3 fw-bold" id="kUsuarios">0</div>
          <div class="text-secondary">Usuarios vinculados</div>
        </div>
        <div class="ms-3 text-success"><i class="bi bi-people fs-1"></i></div>
      </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="fs-3 fw-bold" id="kCapital">$ 0</div>
          <div class="text-secondary">Aportes de capital (VF)</div>
        </div>
        <div class="ms-3 text-primary"><i class="bi bi-cash-coin fs-1"></i></div>
      </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="fs-3 fw-bold" id="kIndustria">$ 0</div>
          <div class="text-secondary">Aportes de industria (VF)</div>
        </div>
        <div class="ms-3 text-warning"><i class="bi bi-tools fs-1"></i></div>
      </div></div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card"><div class="card-body d-flex align-items-center">
        <div class="flex-grow-1">
          <div class="fs-3 fw-bold" id="kTasa">0%</div>
          <div class="text-secondary">Tasa ajustada</div>
        </div>
        <div class="ms-3 text-danger"><i class="bi bi-percent fs-1"></i></div>
      </div></div>
    </div>
  </div>

  {{-- Gráficos --}}
  <div class="row g-3">
    <div class="col-12">
      <div class="card"><div class="card-body">
        <h5 class="mb-3">Inversiones por mes (año actual)</h5>
        <div id="chartMensual"></div>
      </div></div>
    </div>

    <div class="col-md-6">
      <div class="card"><div class="card-body">
        <h5 class="mb-3">% Aportes (por participación)</h5>
        <div id="chartAportes"></div>
      </div></div>
    </div>
    <div class="col-md-6">
      <div class="card"><div class="card-body">
        <h5 class="mb-3">Participación máx vs mín</h5>
        <div id="chartMinMax"></div>
      </div></div>
    </div>
  </div>

  {{-- Tabla resumen por socio --}}
  <div class="card mt-3">
    <div class="card-body">
      <h5 class="mb-3">Resumen</h5>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Socios</th>
              <th class="text-end">Aportes de capital (VF)</th>
              <th class="text-end">Aportes de industria (VF)</th>
              <th class="text-end">Total aportes (VF)</th>
              <th class="text-center">% participación</th>
            </tr>
          </thead>
          <tbody id="tbodyResumen"></tbody>
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
const API = '/api/inversionista/resumen';
const fmtMoney = (n)=> new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n||0);
const fmtPct   = (n)=> (n ?? 0).toFixed(2) + '%';

let chartMensual, chartAportes, chartMinMax;

async function cargar() {
  const p = document.getElementById('proyecto').value;
  const params = new URLSearchParams();
  if (p) params.set('proyecto_id', p);

  const res = await fetch(`${API}?${params.toString()}`);
  if (!res.ok) {
    window.showToast?.('No se pudo cargar el panel', 'danger');
    return;
  }
  const data = await res.json();
  document.getElementById('proyectoNombre').textContent = data.proyecto?.name ?? '—';

  // KPIs
  document.getElementById('kUsuarios').textContent = data.usuarios_vinculados ?? 0;
  document.getElementById('kCapital').textContent  = fmtMoney(data.valor_aportes_capital);
  document.getElementById('kIndustria').textContent= fmtMoney(data.valor_aportes_industria);
  document.getElementById('kTasa').textContent     = fmtPct(data.tasa_ajustada);

  // Tabla
  const tbody = document.getElementById('tbodyResumen');
  tbody.innerHTML = '';
  (data.resumen_usuarios || []).forEach(u=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${u.Nombre}</td>
      <td class="text-end">${fmtMoney(u.Capital)}</td>
      <td class="text-end">${fmtMoney(u.Industria)}</td>
      <td class="text-end">${fmtMoney((u.Capital||0)+(u.Industria||0))}</td>
      <td class="text-center">${(u.Porcentaje||0).toFixed(2)}%</td>
    `;
    tbody.appendChild(tr);
  });
  document.getElementById('tCap').textContent = fmtMoney(data.valor_aportes_capital);
  document.getElementById('tInd').textContent = fmtMoney(data.valor_aportes_industria);
  document.getElementById('tTot').textContent = fmtMoney(data.total_aportes);

  // Charts
  renderMensual(data.mensuales || {capital:Array(12).fill(0), industria:Array(12).fill(0)});
  renderAportes(data.resumen_usuarios || []);
  renderMinMax(data.participacion_minima || 0, data.participacion_maxima || 0);

  window.showToast?.('Panel actualizado', 'success');
}

function renderMensual(m) {
  const opts = {
    chart: { type:'bar', height: 320, toolbar:{show:false} },
    series: [
      { name:'Capital',   data: m.capital   || Array(12).fill(0) },
      { name:'Industria', data: m.industria || Array(12).fill(0) },
    ],
    xaxis: { categories: ['E','F','M','A','M','J','J','A','S','O','N','D'] },
    dataLabels: { enabled:false },
    stroke: { width:2 },
    tooltip: { y:{ formatter:(v)=> fmtMoney(v) } }
  };
  const el = document.querySelector('#chartMensual');
  if (chartMensual) chartMensual.destroy();
  chartMensual = new ApexCharts(el, opts); chartMensual.render();
}

function renderAportes(users) {
  const labels = users.map(u=>u.Nombre);
  const values = users.map(u=>(u.Porcentaje||0));
  const opts = {
    chart: { type:'donut', height: 320 },
    labels, series: values,
    dataLabels: { enabled: true, formatter: (v)=> v.toFixed(1)+'%' },
    tooltip: { y:{ formatter:(v)=> v.toFixed(2)+'%' } },
    legend: { position:'bottom' }
  };
  const el = document.querySelector('#chartAportes');
  if (chartAportes) chartAportes.destroy();
  chartAportes = new ApexCharts(el, opts); chartAportes.render();
}

function renderMinMax(min, max) {
  const opts = {
    chart: { type:'bar', height: 320, toolbar:{show:false} },
    series: [{ name:'Participación', data:[min, max] }],
    xaxis: { categories:['Mínima','Máxima'] },
    dataLabels: { enabled:true, formatter:(v)=> v.toFixed(2)+'%' },
  };
  const el = document.querySelector('#chartMinMax');
  if (chartMinMax) chartMinMax.destroy();
  chartMinMax = new ApexCharts(el, opts); chartMinMax.render();
}

document.addEventListener('DOMContentLoaded', ()=>{
  document.getElementById('btnAplicar').addEventListener('click', cargar);
  cargar();
});
</script>
@endsection
