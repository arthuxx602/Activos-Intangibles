@extends('layouts.app')

@section('title','Resumen')
@section('page-title','Resumen')

@section('content')
@if(!empty($error))
  <div class="alert alert-warning">{{ $error }}</div>
@else

<div class="row g-3 pb-3">
  <div class="col-xl-3 col-lg-3 col-md-6">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold fs-5">{{ $kpis['inversiones'] }}</div>
          <div class="text-secondary small">Inversiones realizadas</div>
        </div>
        <i class="bi bi-calendar2"></i>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-6">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold fs-5">{{ $kpis['vida_dias'] }} días</div>
          <div class="text-secondary small">Vida del proyecto</div>
        </div>
        <i class="bi bi-heart-fill"></i>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-6">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold fs-5">{{ $kpis['personas'] }}</div>
          <div class="text-secondary small">Inversionistas</div>
        </div>
        <i class="bi bi-people"></i>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-6">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold fs-5">{{ number_format($kpis['tasa'],2,',','.') }}%</div>
          <div class="text-secondary small">Tasa ajustada</div>
        </div>
        <i class="bi bi-cash-coin"></i>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="text-primary">Resumen inversiones</h5>
        <table class="table mt-3">
          <tbody>
            <tr>
              <th>Valor de los aportes de capital</th>
              <td class="text-end">$ {{ number_format($totales['capital'],0,',','.') }}</td>
            </tr>
            <tr>
              <th>Valor de los aportes de industria</th>
              <td class="text-end">$ {{ number_format($totales['industria'],0,',','.') }}</td>
            </tr>
            <tr>
              <th>Total Aportes</th>
              <td class="text-end fw-bold">$ {{ number_format($totales['aportes'],0,',','.') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="text-primary">Gráfico tipo de aporte</h5>

        {{-- Pasamos el JSON por data-attribute para que el editor no se queje --}}
        <div id="chartAportes"
             data-chart='@json($chartValor ?? ["labels"=>[],"series"=>[]])'
             style="height:300px;"></div>
      </div>
    </div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-body">
    <h5 class="text-primary">Aportes en dinero</h5>
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>Fecha del aporte</th>
            <th class="text-end">Valor del aporte</th>
            <th class="text-end">Valor ajustado</th>
            <th>Tiempo transcurrido</th>
          </tr>
        </thead>
        <tbody>
          @forelse($dineroRows as $row)
            <tr>
              <td>{{ $row['fecha'] }}</td>
              <td class="text-end">$ {{ number_format($row['monto'],0,',','.') }}</td>
              <td class="text-end">$ {{ number_format($row['ajustado'],0,',','.') }}</td>
              <td>{{ $row['tiempo'] }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-secondary">Sin registros</td></tr>
          @endforelse
          <tr class="table-primary">
            <th>Total</th>
            <th></th>
            <th class="text-end">$ {{ number_format($dineroTotalAjustado,0,',','.') }}</th>
            <th></th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-body">
    <h5 class="text-primary">Aportes en especie</h5>
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>Fecha del aporte</th>
            <th class="text-end">Total aporte en especie</th>
            <th class="text-end">Total ajustado</th>
            <th>Tiempo transcurrido</th>
          </tr>
        </thead>
        <tbody>
          @forelse($especieRows as $row)
            <tr>
              <td>{{ $row['fecha'] }}</td>
              <td class="text-end">$ {{ number_format($row['total'],0,',','.') }}</td>
              <td class="text-end">$ {{ number_format($row['ajustado'],0,',','.') }}</td>
              <td>{{ $row['tiempo'] }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-secondary">Sin registros</td></tr>
          @endforelse
          <tr class="table-primary">
            <th>Total:</th>
            <th></th>
            <th class="text-end">$ {{ number_format($especieTotalAjustado,0,',','.') }}</th>
            <th></th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('chartAportes');
    if (!el || typeof ApexCharts === 'undefined') return;

    // Leer JSON seguro desde el data-attribute
    let chartData = { labels: [], series: [] };
    try {
      chartData = JSON.parse(el.dataset.chart || '{"labels":[],"series":[]}');
      if (!Array.isArray(chartData.labels)) chartData.labels = [];
      if (!Array.isArray(chartData.series)) chartData.series = [];
    } catch (e) { /* nos quedamos con defaults */ }

    const chart = new ApexCharts(el, {
      chart: { type: 'donut', height: 300 },
      labels: chartData.labels,
      series: chartData.series,
      legend: { position: 'bottom' },
      dataLabels: { enabled: true }
    });
    chart.render();
  });
</script>
@endsection
