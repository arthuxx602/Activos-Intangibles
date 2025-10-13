@extends('layouts.app')

@section('title','Inversionista')
@section('page-title','Inversionista')

@section('head')
  <style>
    .card { border-radius: 14px; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
    .stat { font-weight:700; font-size:1.25rem; }
    .monos { font-variant-numeric: tabular-nums; }
  </style>
@endsection

@section('content')
<div class="container-fluid">

  {{-- Filtros --}}
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Usuario</label>
          <select id="fUsuario" class="form-select"></select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Proyecto (opcional)</label>
          <select id="fProyecto" class="form-select"></select>
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2">
          <button class="btn btn-primary" id="btnAplicar">Aplicar</button>
          <button class="btn btn-outline-secondary" id="btnLimpiar">Limpiar</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Totales --}}
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-secondary">Aportes de capital</div>
          <div class="stat monos" id="totCapital">$ 0</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-secondary">Aportes de industria</div>
          <div class="stat monos" id="totIndustria">$ 0</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-secondary">Total aportes</div>
          <div class="stat monos" id="totAportes">$ 0</div>
          <div class="small text-secondary">Tasa ajustada: <span id="tasaLbl">0%</span></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Detalle: Dinero/Especie (Capital) --}}
  <div class="card mt-3">
    <div class="card-body">
      <h6 class="mb-3">Detalle - Capital (Dinero y Especie)</h6>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th>Fecha</th>
              <th class="text-end">Monto</th>
              <th class="text-end">Valor ajustado</th>
              <th class="text-center">Días</th>
              <th>Proyecto</th>
            </tr>
          </thead>
          <tbody id="tbodyCapital"></tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Detalle: Industria --}}
  <div class="card mt-3">
    <div class="card-body">
      <h6 class="mb-3">Detalle - Industria</h6>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead class="table-light">
            <tr>
              <th>Fecha</th>
              <th class="text-end">Monto</th>
              <th class="text-end">Valor ajustado</th>
              <th class="text-center">Días</th>
              <th>Proyecto</th>
            </tr>
          </thead>
          <tbody id="tbodyIndustria"></tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
const API = '/api';

// helpers
const $ = s => document.querySelector(s);
const fmt = n => new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n||0);

// cargar catálogos
async function cargarCatalogos(){
  try{
    const res = await fetch(`${API}/inversionista/catalogos`);
    const {usuarios, proyectos} = await res.json();

    const su = $('#fUsuario'); su.innerHTML = `<option value="">Seleccione...</option>`;
    usuarios.forEach(u=>{
      const opt = new Option(`${u.ID_Usuario} - ${u.Nombre} ${u.Apellido||''}`, u.ID_Usuario);
      su.add(opt);
    });

    const sp = $('#fProyecto'); sp.innerHTML = `<option value="">Todos</option>`;
    proyectos.forEach(p=>{
      const opt = new Option(`${p.ID_Proyecto} - ${p.Nombre}${p.liquidado ? ' (liquidado)':''}`, p.ID_Proyecto);
      sp.add(opt);
    });

  }catch(e){
    console.error(e);
    window.showToast('No se pudieron cargar los catálogos','danger');
  }
}

async function aplicar(){
  const usuario = $('#fUsuario').value;
  const proyecto = $('#fProyecto').value;

  if(!usuario){
    window.showToast('Selecciona un usuario','warning');
    return;
  }

  try{
    const url = new URL(`${API}/inversionista/resumen`, window.location.origin);
    url.searchParams.set('usuario', usuario);
    if(proyecto) url.searchParams.set('proyecto', proyecto);

    const res = await fetch(url);
    const data = await res.json();

    // totales
    $('#tasaLbl').textContent = (data.tasa || 0).toFixed(2) + '%';
    $('#totCapital').textContent   = fmt(data.totales.capital);
    $('#totIndustria').textContent = fmt(data.totales.industria);
    $('#totAportes').textContent   = fmt(data.totales.aportes);

    // tablas
    renderTabla('#tbodyCapital',   (data.detalle.dinero || []));
    renderTabla('#tbodyIndustria', (data.detalle.industria || []));

    window.showToast('Resumen cargado','success');
  }catch(e){
    console.error(e);
    window.showToast('Error consultando el resumen','danger');
  }
}

function renderTabla(tbodySel, rows){
  const tb = $(tbodySel);
  tb.innerHTML = '';
  rows.forEach(r=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.Fecha || ''}</td>
      <td class="text-end">${fmt(r.Monto)}</td>
      <td class="text-end">${fmt(r.ValorAjustado)}</td>
      <td class="text-center">${r.Dias}</td>
      <td>${r.Proyecto || ''}</td>
    `;
    tb.appendChild(tr);
  });
}

document.addEventListener('DOMContentLoaded',()=>{
  cargarCatalogos();
  $('#btnAplicar').addEventListener('click', aplicar);
  $('#btnLimpiar').addEventListener('click', ()=>{
    $('#fUsuario').value = '';
    $('#fProyecto').value = '';
    $('#tbodyCapital').innerHTML = '';
    $('#tbodyIndustria').innerHTML = '';
    $('#totCapital').textContent = $('#totIndustria').textContent = $('#totAportes').textContent = '$ 0';
    $('#tasaLbl').textContent = '0%';
  });
});
</script>
@endsection
