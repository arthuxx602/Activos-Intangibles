@extends('admin.layouts.app')

@section('title','Inicio')
@section('page-title','Inicio')

@section('content')
<div class="row align-items-center g-4 mb-4">
  <div class="col-md-4 text-center text-md-start">
    <img class="img-fluid rounded" src="https://dummyimage.com/480x240/eff2f7/6c757d&text=Bienvenido" alt="Bienvenido">
  </div>
  <div class="col-md-4">
    <h5 class="mb-2">Bienvenido de nuevo</h5>
    <div class="fs-4 text-primary fw-bold" id="nombreUsuario">Usuario</div>
    <p class="text-secondary mb-0">¡Nos alegra verte! Estamos listos para ayudarte con tus proyectos e inversiones.</p>
  </div>
  <div class="col-md-4 text-center">
    <h6 class="text-muted mb-1">Hora actual en Colombia</h6>
    <iframe src="https://www.zeitverschiebung.net/clock-widget-iframe-v2?language=es&size=medium&timezone=America%2FBogota"
            width="100%" height="115" frameborder="0" seamless></iframe>
  </div>
</div>

<div class="row g-3" id="cards">
  <div class="col-sm-6 col-lg-3">
    <div class="card"><div class="card-body d-flex align-items-center">
      <div class="flex-grow-1">
        <div class="fs-3 fw-bold" id="totalProyectos">0</div>
        <div class="text-secondary">Empresas</div>
      </div>
      <div class="ms-3 text-info"><i class="bi bi-building fs-1"></i></div>
    </div></div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card"><div class="card-body d-flex align-items-center">
      <div class="flex-grow-1">
        <div class="text-secondary mb-1">Inicio primera empresa</div>
        <div class="fw-semibold" id="fechaMasAntigua">—</div>
      </div>
      <div class="ms-3 text-danger"><i class="bi bi-calendar2-week fs-1"></i></div>
    </div></div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card"><div class="card-body d-flex align-items-center">
      <div class="flex-grow-1">
        <div class="text-secondary mb-1">Inicio última empresa</div>
        <div class="fw-semibold" id="fechaMasNueva">—</div>
      </div>
      <div class="ms-3 text-warning"><i class="bi bi-calendar2-event fs-1"></i></div>
    </div></div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card"><div class="card-body d-flex align-items-center">
      <div class="flex-grow-1">
        <div class="fs-3 fw-bold" id="totalUsuarios">0</div>
        <div class="text-secondary">Usuarios</div>
      </div>
      <div class="ms-3 text-success"><i class="bi bi-people fs-1"></i></div>
    </div></div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">Empresas creadas por mes</h5>
      <div class="d-flex align-items-center gap-2">
        <label class="me-2 text-secondary">Año</label>
        <select id="chartYear" class="form-select form-select-sm" style="width:auto"></select>
      </div>
    </div>
    <div id="chartProyectosMes"></div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
const API='/api';
const nombre = localStorage.getItem('nombre') || 'Arturo';
const apellido = localStorage.getItem('apellido') || 'Marquez';
document.getElementById('nombreUsuario').textContent = `${nombre} ${apellido}`;

async function cargarDashboard(){
  try{
    const r = await fetch(`${API}/dashboard/summary`);
    const d = await r.json();
    totalProyectos.textContent = d.total_proyectos ?? 0;
    fechaMasAntigua.textContent = d.fecha_mas_antigua ?? '—';
    fechaMasNueva.textContent   = d.fecha_mas_nueva ?? '—';
    totalUsuarios.textContent   = d.total_usuarios ?? 0;
  }catch(e){ toastError('No se pudo cargar el dashboard'); }
}

let chart;
function poblarAnios(){
  const sel = document.getElementById('chartYear');
  const y = new Date().getFullYear();
  sel.innerHTML = '';
  for(let i=y;i>=y-5;i--){
    const o = new Option(i,i);
    sel.add(o);
  }
  sel.value = y;
}
async function cargarGrafico(year){
  try{
    const r = await fetch(`${API}/dashboard/proyectos-por-mes?year=${year}`);
    const d = await r.json();
    const opts = {
      chart:{type:'bar',height:320,toolbar:{show:false}},
      series:[{name:`Empresas ${d.year}`,data:d.series}],
      xaxis:{categories:d.labels},
      plotOptions:{bar:{borderRadius:6}},
      dataLabels:{enabled:false}
    };
    const el = document.getElementById('chartProyectosMes');
    if(chart) chart.destroy();
    chart = new ApexCharts(el, opts); chart.render();
  }catch(e){ toastError('No se pudo cargar la gráfica'); }
}

document.addEventListener('DOMContentLoaded',()=>{
  cargarDashboard();
  poblarAnios();
  const sel=document.getElementById('chartYear');
  cargarGrafico(sel.value);
  sel.addEventListener('change',e=>cargarGrafico(e.target.value));
});
</script>
@endpush
