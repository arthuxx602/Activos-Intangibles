@extends('layouts.app')

@section('title', 'Inversionista | Evolución anual')

@section('page-title', 'Evolución anual de inversiones (2018–2030)')

@section('head')
  <style>
    .card { border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
  </style>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <div class="row g-2 align-items-end mb-3">
      <div class="col-md-5">
        <label class="form-label">Proyecto</label>
        <select id="proyecto" class="form-select">
          @foreach($proyectos as $p)
            <option value="{{ $p->ID_Proyecto }}">{{ $p->ID_Proyecto }} - {{ $p->Nombre }}</option>
          @endforeach
        </select>
        <div class="form-text">Si no seleccionas, intentaremos usar el proyecto vinculado a tu usuario (sesión).</div>
      </div>
      <div class="col-md-3">
        <button id="btnAplicar" class="btn btn-primary w-100">
          <i class="bi bi-arrow-repeat me-1"></i> Actualizar
        </button>
      </div>
    </div>

    <div id="chartLineAnual"></div>
  </div>
</div>
@endsection

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
    const API = '/api/inversionista/datos-line-anual';
    let chart;

    function renderChart(payload) {
      const opts = {
        chart: { type: 'line', height: 360, toolbar: { show:false } },
        series: payload.series,
        xaxis: { categories: payload.labels, title: { text: 'Año' } },
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        tooltip: {
          y: {
            formatter: (v)=> new Intl.NumberFormat('es-CO', {
              maximumFractionDigits: 2
            }).format(v) + ' M'
          }
        },
        yaxis: {
          labels: {
            formatter: (v)=> new Intl.NumberFormat('es-CO', {
              maximumFractionDigits: 2
            }).format(v) + ' M'
          },
          title: { text: 'Monto (Millones)' }
        },
        markers: { size: 3 },
      };

      const el = document.querySelector('#chartLineAnual');
      if (chart) chart.destroy();
      chart = new ApexCharts(el, opts);
      chart.render();
    }

    async function cargar() {
      const p = document.getElementById('proyecto').value;
      const params = new URLSearchParams();
      if (p) params.set('proyecto_id', p);

      const res = await fetch(`${API}?${params.toString()}`);
      if (!res.ok) throw new Error('Error al consultar datos');

      const json = await res.json();
      renderChart(json);
      window.showToast?.('Gráfico actualizado', 'success');
    }

    document.addEventListener('DOMContentLoaded', async ()=>{
      try {
        await cargar();
      } catch (e) {
        console.error(e);
        window.showToast?.('No se pudo cargar el gráfico', 'danger');
      }

      document.getElementById('btnAplicar').addEventListener('click', cargar);
    });
  </script>
@endsection
