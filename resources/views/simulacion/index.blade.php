@extends('layouts.app')

@section('title','Simulación')
@section('page-title','Simulación')

@section('content')
<div class="card mb-3">
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-sm-6">
        <label class="form-label">Proyecto</label>
        <select id="selProyecto" class="form-select"></select>
      </div>
      <div class="col-sm-6 d-flex gap-2">
        <button class="btn btn-primary" onclick="cargar()">Calcular</button>
        <button class="btn btn-outline-secondary" onclick="limpiar()">Limpiar</button>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card"><div class="card-body">
      <h6 class="mb-3">Aportes por tipo (%)</h6>
      <div id="chartTipos"></div>
    </div></div>
  </div>
  <div class="col-lg-6">
    <div class="card"><div class="card-body">
      <h6 class="mb-3">Usuarios vinculados</h6>
      <div id="chartUsuarios"></div>
    </div></div>
  </div>
  <div class="col-12">
    <div class="card"><div class="card-body">
      <h6 class="mb-3">Inversiones por mes</h6>
      <div id="chartMes"></div>
    </div></div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-body">
    <h6 class="mb-3">Resumen</h6>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr>
          <th>Socio</th><th class="text-end">Capital</th><th class="text-end">Industria</th><th class="text-end">Total</th><th class="text-center">%</th>
        </tr></thead>
        <tbody id="tbody"></tbody>
        <tfoot><tr>
          <th>Totales</th>
          <th id="tCap" class="text-end">—</th>
          <th id="tInd" class="text-end">—</th>
          <th id="tTot" class="text-end">—</th>
          <th class="text-center">100%</th>
        </tr></tfoot>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
const API='/api', $=s=>document.querySelector(s);
let chartTipos, chartUsuarios, chartMes;

async function cargarCatalogo(){
  const r = await fetch(`${API}/catalogos/proyectos-no-liquidados`);
  const d = await r.json();
  selProyecto.innerHTML = `<option value="">Seleccione...</option>`;
  (d||[]).forEach(p=> selProyecto.add(new Option(`${p.ID_Proyecto} - ${p.Nombre}`, p.ID_Proyecto)));
}
function limpiar(){
  $('#tbody').innerHTML=''; tCap.textContent=tInd.textContent=tTot.textContent='—';
  if(chartTipos) chartTipos.destroy(); if(chartUsuarios) chartUsuarios.destroy(); if(chartMes) chartMes.destroy();
}
async function cargar(){
  const id = selProyecto.value; if(!id) return toastWarn('Selecciona un proyecto');
  try{
    const r = await fetch(`${API}/simulaciones/resumen?proyecto=${id}`); // Implementa este endpoint en tu SimulacionController
    const d = await r.json();

    // tabla
    const tb=$('#tbody'); tb.innerHTML=''; 
    let cap=0, ind=0, tot=0;
    d.detalle.forEach(x=>{
      cap+=x.capital; ind+=x.industria; tot+=x.capital+x.industria;
      const tr=document.createElement('tr');
      tr.innerHTML=`
        <td>${x.nombre}</td>
        <td class="text-end">${fmt(x.capital)}</td>
        <td class="text-end">${fmt(x.industria)}</td>
        <td class="text-end">${fmt(x.capital+x.industria)}</td>
        <td class="text-center">${(x.porcentaje||0).toFixed(2)}%</td>`;
      tb.appendChild(tr);
    });
    tCap.textContent=fmt(cap); tInd.textContent=fmt(ind); tTot.textContent=fmt(tot);

    // charts
    chartTipos = renderPie('chartTipos', ['Capital','Industria'], [d.porcentajes.capital, d.porcentajes.industria]);
    chartUsuarios = renderRadial('chartUsuarios', d.usuarios_vinculados || 0);
    chartMes = renderBar('chartMes', d.mensual.labels, d.mensual.series, 'Inversiones');

    toastSuccess('Simulación lista');
  }catch(e){ toastError('No se pudo simular'); }
}
function fmt(n){ return new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n||0); }
function renderPie(el,labels,series){
  const o={chart:{type:'donut',height:280},labels,series,legend:{position:'bottom'},dataLabels:{enabled:true}};
  const c=new ApexCharts(document.getElementById(el),o); c.render(); return c;
}
function renderRadial(el,value){
  const o={chart:{height:280,type:'radialBar'},series:[value],labels:['Usuarios'],plotOptions:{radialBar:{dataLabels:{value:{formatter:v=>v}}}}};
  const c=new ApexCharts(document.getElementById(el),o); c.render(); return c;
}
function renderBar(el,categories,series,name){
  const o={chart:{type:'bar',height:300},xaxis:{categories},series:[{name,data:series}],dataLabels:{enabled:false}};
  const c=new ApexCharts(document.getElementById(el),o); c.render(); return c;
}
document.addEventListener('DOMContentLoaded', cargarCatalogo);
</script>
@endpush
