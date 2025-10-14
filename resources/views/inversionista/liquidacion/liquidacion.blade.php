@extends('layouts.app')

@section('title','Inversionista | Liquidación')
@section('page-title','Liquidación')

@section('content')
  <div class="card mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Proyecto</label>
          <select id="proyecto" class="form-select">
            <option value="">Auto (vinculado por usuario)</option>
            @foreach($proyectos as $p)
              <option value="{{ $p->ID_Proyecto }}" @selected($proyectoIdSesion==$p->ID_Proyecto)>{{ $p->ID_Proyecto }} - {{ $p->Nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <button id="btnCalcular" class="btn btn-primary w-100">
            <i class="bi bi-calculator me-1"></i> Calcular
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- KPIs --}}
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card"><div class="card-body">
        <div class="text-secondary">Total aportes</div>
        <div class="fs-3 fw-bold" id="kAportes">$ 0</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card"><div class="card-body">
        <div class="text-secondary">Rendimientos</div>
        <div class="fs-3 fw-bold" id="kRendimientos">$ 0</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card"><div class="card-body">
        <div class="text-secondary">Total a liquidar</div>
        <div class="fs-3 fw-bold" id="kLiquidar">$ 0</div>
      </div></div>
    </div>
  </div>

  {{-- Tabla detalle --}}
  <div class="card">
    <div class="card-body">
      <h5 class="mb-3">Detalle por socio</h5>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Socio</th>
              <th class="text-end">Aporte</th>
              <th class="text-end">Rendimiento</th>
              <th class="text-end">A liquidar</th>
              <th class="text-center">% participación</th>
            </tr>
          </thead>
          <tbody id="tbodyDetalle"></tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
const API = '/api/inversionista/liquidacion/resumen';
const fmtMoney = (n)=> new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(n||0);

async function calcular() {
  const p = document.getElementById('proyecto').value;
  const qs = p ? `?proyecto_id=${p}` : '';
  const res = await fetch(`${API}${qs}`);
  if (!res.ok) { window.showToast?.('No se pudo calcular', 'danger'); return; }
  const data = await res.json();

  // KPIs
  document.getElementById('kAportes').textContent      = fmtMoney(data.resumen?.totalAportes || 0);
  document.getElementById('kRendimientos').textContent = fmtMoney(data.resumen?.totalRendimientos || 0);
  document.getElementById('kLiquidar').textContent     = fmtMoney(data.resumen?.totalLiquidar || 0);

  // Detalle
  const tb = document.getElementById('tbodyDetalle');
  tb.innerHTML = '';
  (data.detalle || []).forEach(row=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${row.nombre}</td>
      <td class="text-end">${fmtMoney(row.aporte)}</td>
      <td class="text-end">${fmtMoney(row.rendimiento)}</td>
      <td class="text-end">${fmtMoney(row.a_liquidar)}</td>
      <td class="text-center">${(row.porcentaje||0).toFixed(2)}%</td>
    `;
    tb.appendChild(tr);
  });

  window.showToast?.('Liquidación actualizada', 'success');
}

document.addEventListener('DOMContentLoaded', ()=>{
  document.getElementById('btnCalcular').addEventListener('click', calcular);
  calcular();
});
</script>
@endsection
