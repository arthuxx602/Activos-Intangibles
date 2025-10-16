@extends('layouts.moderador')
@section('title','Línea anual de inversiones')

@section('content')
<div class="container-fluid">
  <div class="mb-3">
    <h2 class="h4">Inversiones anuales — Proyecto: {{ $proyectoNombre ?? '—' }}</h2>
  </div>

  <div class="card">
    <div class="card-body">
      <div id="lineAnnual"></div>
    </div>
  </div>
</div>

{{-- Highcharts (puedes moverlo a Vite si ya lo empacas) --}}
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
(async function(){
  const res = await fetch('{{ route('moderador.estadisticas.datos-line') }}', {headers:{'X-Requested-With':'XMLHttpRequest'}});
  const data = await res.json();

  if (data.error) {
    document.getElementById('lineAnnual').innerHTML = '<div class="text-danger">'+data.error+'</div>';
    return;
  }

  const years = Array.from({length: 13}, (_,i)=> 2018 + i);

  Highcharts.chart('lineAnnual', {
    title: { text: 'Totales anuales por tipo (millones)' },
    xAxis: { categories: years, title: { text: 'Año' } },
    yAxis: { title: { text: 'Millones' } },
    tooltip: { shared: true, valueDecimals: 2, valueSuffix: ' M' },
    series: [
      { name: 'Dinero',    data: data.dinero    || [] },
      { name: 'Especie',   data: data.especie   || [] },
      { name: 'Industria', data: data.industria || [] },
    ]
  });
})();
</script>
@endsection
