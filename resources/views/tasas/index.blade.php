@extends('layouts.app')
@section('title','tasas')
@section('page-title','tasas')
@section('content')
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tasas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background:#f8fafc; font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto; }
    .card { border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
    .readonly { background: #f1f3f5; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex align-items-end justify-content-between mb-3">
    <div>
      <h3 class="mb-1">Tasas</h3>
      <div class="text-secondary">Calcula variables y guarda la <strong>tasa de descuento ajustada al riesgo</strong>.</div>
    </div>
    <div class="text-end">
      <div class="text-secondary small">Última tasa guardada</div>
      <div class="h4 mb-0"><span id="ultimaTasa">—</span></div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <form id="formTasas" onsubmit="return false;">
            <div class="row g-3">
              {{-- Entradas --}}
              <div class="col-md-6">
                <h6 class="mb-2">Entradas</h6>

                <label class="form-label">Tasa Libre de Riesgo EEUU (%)</label>
                <input id="tlr" type="number" step="any" class="form-control" placeholder="%">

                <label class="form-label mt-3">Tasa Libre de Riesgo Colombia (%)</label>
                <input id="tlrc" type="number" step="any" class="form-control" placeholder="%">

                <label class="form-label mt-3">Retorno S&amp;P 500 (%)</label>
                <input id="rsyp" type="number" step="any" class="form-control" placeholder="%">

                <label class="form-label mt-3">Beta Desapalancado</label>
                <input id="bd" type="number" step="any" class="form-control" placeholder="">

                <label class="form-label mt-3">Tasa de impuestos (%)</label>
                <input id="tdi" type="number" step="any" class="form-control" placeholder="%">

                <label class="form-label mt-3">Activo (A)</label>
                <input id="a" type="number" step="any" class="form-control" placeholder="">

                <label class="form-label mt-3">Deuda (D)</label>
                <input id="d" type="number" step="any" class="form-control" placeholder="">

                <label class="form-label mt-3">Patrimonio (P)</label>
                <input id="p" type="number" step="any" class="form-control" placeholder="">

                <label class="form-label mt-3">Devaluación esperada (%)</label>
                <input id="de" type="number" step="any" class="form-control" placeholder="%">

                <label class="form-label mt-3">Prima por tamaño (%)</label>
                <input id="ppt" type="number" step="any" class="form-control" placeholder="%">

                <label class="form-label mt-3">Costo deuda antes de impuestos (%)</label>
                <input id="cdadi" type="number" step="any" class="form-control" placeholder="%">

                <label class="form-label mt-3">Spread de ajuste (%)</label>
                <input id="sda" type="number" step="any" class="form-control" placeholder="%">

                <div class="d-grid mt-4">
                  <button id="btnCalcular" class="btn btn-secondary" type="button">Calcular</button>
                </div>
              </div>

              {{-- Resultados --}}
              <div class="col-md-6">
                <h6 class="mb-2">Resultados</h6>

                <div class="row g-2">
                  <div class="col-6">
                    <label class="form-label">TLR</label>
                    <input id="tlr2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">TLR Col</label>
                    <input id="tlrc2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">Riesgo País</label>
                    <input id="rp2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">Retorno S&amp;P</label>
                    <input id="rsyp2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">RLR</label>
                    <input id="rlr2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">Prima Mercado</label>
                    <input id="pdm2" class="form-control readonly" readonly>
                  </div>

                  <div class="col-6">
                    <label class="form-label">Beta (desap.)</label>
                    <input id="bd2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">Tasa Impuestos</label>
                    <input id="tdi2" class="form-control readonly" readonly>
                  </div>

                  <div class="col-4">
                    <label class="form-label">A</label>
                    <input id="a2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-4">
                    <label class="form-label">D</label>
                    <input id="d2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-4">
                    <label class="form-label">P</label>
                    <input id="p2" class="form-control readonly" readonly>
                  </div>

                  <div class="col-4">
                    <label class="form-label">D/A</label>
                    <input id="da2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-4">
                    <label class="form-label">P/A</label>
                    <input id="pa2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-4">
                    <label class="form-label">D/P</label>
                    <input id="dp2" class="form-control readonly" readonly>
                  </div>

                  <div class="col-6">
                    <label class="form-label">Beta (apal.)</label>
                    <input id="ba2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">CE en USD</label>
                    <input id="cepi2" class="form-control readonly" readonly>
                  </div>

                  <div class="col-6">
                    <label class="form-label">Devaluación</label>
                    <input id="de2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">CE en COP (Ke)</label>
                    <input id="cepiep2" class="form-control readonly" readonly>
                  </div>

                  <div class="col-6">
                    <label class="form-label">Prima tamaño</label>
                    <input id="ppt2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">Ke + prima</label>
                    <input id="cepeiep2" class="form-control readonly" readonly>
                  </div>

                  <div class="col-6">
                    <label class="form-label">Costo deuda (antes)</label>
                    <input id="cdadi2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">Costo deuda (después)</label>
                    <input id="cdddi2" class="form-control readonly" readonly>
                  </div>

                  <div class="col-6">
                    <label class="form-label">Tasa descuento</label>
                    <input id="tdd2" class="form-control readonly" readonly>
                  </div>
                  <div class="col-6">
                    <label class="form-label">Spread ajuste</label>
                    <input id="sda2" class="form-control readonly" readonly>
                  </div>

                  <div class="col-12">
                    <label class="form-label">Tasa de descuento ajustada al riesgo</label>
                    <input id="tddaar2" class="form-control form-control-lg readonly" readonly>
                    <div class="form-text">Este valor se guardará en la tabla <code>tasa</code> como <strong>Tasa</strong> (%).</div>
                  </div>

                  <div class="col-12 d-grid mt-2">
                    <button id="btnGuardar" class="btn btn-primary" type="button">Guardar tasa</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div><!-- card-body -->
      </div><!-- card -->
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-3">Historial de tasas</h6>
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead>
                <tr><th>Fecha</th><th class="text-end">Tasa</th></tr>
              </thead>
              <tbody id="tbodyHistorial"></tbody>
            </table>
          </div>
          <button class="btn btn-outline-secondary btn-sm" id="btnRefrescar">Refrescar</button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const API = '/api';
  const $ = (id) => document.getElementById(id);
  const toPct = (v) => (v*100).toFixed(2) + '%';
  const porcentajeADecimal = (v) => (parseFloat(v)||0)/100;

  async function cargarUltima() {
    const res = await fetch(`${API}/tasas/ultima`);
    if (res.status === 204) { $('ultimaTasa').textContent = '—'; return; }
    const t = await res.json();
    $('ultimaTasa').textContent = (t.Tasa ?? 0).toFixed(2) + '%';
  }

  async function cargarHistorial() {
    const res = await fetch(`${API}/tasas?per_page=10`);
    const data = await res.json();
    const tbody = $('tbodyHistorial');
    tbody.innerHTML = '';
    (data.data || []).forEach(row => {
      const tr = document.createElement('tr');
      const fecha = row.Fecha ? new Date(row.Fecha).toLocaleString() : '—';
      tr.innerHTML = `<td>${fecha}</td><td class="text-end">${(row.Tasa ?? 0).toFixed(2)}%</td>`;
      tbody.appendChild(tr);
    });
  }

  function calcular() {
    const tlr2  = porcentajeADecimal($('tlr').value);
    const tlrc2 = porcentajeADecimal($('tlrc').value);
    const rsyp2 = porcentajeADecimal($('rsyp').value);
    const bd2   = parseFloat($('bd').value) || 0;
    const tdi2  = porcentajeADecimal($('tdi').value);
    const a2v   = parseFloat($('a').value) || 0;
    const d2v   = parseFloat($('d').value) || 0;
    const p2v   = parseFloat($('p').value) || 0;
    const de2v  = porcentajeADecimal($('de').value);
    const ppt2  = porcentajeADecimal($('ppt').value);
    const cdadi2= porcentajeADecimal($('cdadi').value);
    const sda2  = porcentajeADecimal($('sda').value);

    const rp2   = tlrc2 - tlr2;
    const rlr2  = tlrc2;
    const pdm2  = rsyp2 - tlrc2;

    const da2   = a2v ? (d2v / a2v) : 0;
    const pa2   = a2v ? (p2v / a2v) : 0;
    const dp2   = p2v ? (d2v / p2v) : 0;

    const ba2   = bd2 * (1 + (dp2 * (1 - tdi2)));
    const cepi2 = tlr2 + ba2 * pdm2 + rp2;
    const cepiep2  = (1 + cepi2) * (1 + de2v) - 1;
    const cepeiep2 = cepiep2 + ppt2;
    const cdddi2   = cdadi2 * (1 - tdi2);
    const tdd2     = pa2 * cepeiep2 + da2 * cdddi2;
    const tddaar2  = tdd2 + sda2;

    $('tlr2').value     = toPct(tlr2);
    $('tlrc2').value    = toPct(tlrc2);
    $('rp2').value      = toPct(rp2);
    $('rsyp2').value    = toPct(rsyp2);
    $('rlr2').value     = toPct(rlr2);
    $('pdm2').value     = toPct(pdm2);
    $('bd2').value      = (bd2).toFixed(2);
    $('tdi2').value     = toPct(tdi2);
    $('da2').value      = toPct(da2);
    $('pa2').value      = toPct(pa2);
    $('a2').value       = (a2v).toFixed(0);
    $('d2').value       = (d2v).toFixed(0);
    $('p2').value       = (p2v).toFixed(0);
    $('dp2').value      = (dp2).toFixed(2);
    $('ba2').value      = (ba2).toFixed(2);
    $('cepi2').value    = toPct(cepi2);
    $('cepiep2').value  = toPct(cepiep2);
    $('cepeiep2').value = toPct(cepeiep2);
    $('cdadi2').value   = toPct(cdadi2);
    $('cdddi2').value   = toPct(cdddi2);
    $('tdd2').value     = toPct(tdd2);
    $('sda2').value     = toPct(sda2);
    $('de2').value      = toPct(de2v);
    $('ppt2').value     = toPct(ppt2);

    $('tddaar2').value  = toPct(tddaar2);
    return tddaar2 * 100; // devuelve en % para guardar
  }

  async function guardar() {
    const oblig = ['tlr','tlrc','rsyp','bd','tdi','a','d','p','de','ppt','cdadi','sda'];
    for (const c of oblig) {
      if ($(c).value === '') { alert('Hay campos en blanco. Complétalos antes de guardar.'); return; }
    }
    const tasaFinal = calcular(); // en %
    const res = await fetch(`${API}/tasas`, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ Tasa: tasaFinal })
    });
    if (!res.ok) {
      const t = await res.text();
      alert('Error al guardar la tasa.\n' + t);
      return;
    }
    alert('Datos guardados correctamente.');
    await cargarUltima();
    await cargarHistorial();
  }

  document.addEventListener('DOMContentLoaded', async () => {
    await cargarUltima();
    await cargarHistorial();
    $('btnCalcular').addEventListener('click', calcular);
    $('btnGuardar').addEventListener('click', guardar);
    $('btnRefrescar').addEventListener('click', async ()=> {
      await cargarUltima();
      await cargarHistorial();
    });
  });
</script>
</body>
</html>
@endsection('content')
