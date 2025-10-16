@extends('layouts.moderador')
@section('title','Inicio')

@section('content')
<div class="pd-ltr-20">

  {{-- Banner bienvenida --}}
  <div class="card-box pd-20 mb-30">
    <div class="row align-items-center">
      <div class="col-md-4">
        <img src="{{ asset('vendors/images/banner-img.png') }}" alt="banner" class="img-fluid" />
      </div>

      <div class="col-md-4">
        @php
          $nombre = session('nombre','');
          $apellido = session('apellido','');
        @endphp
        <h4 class="font-20 weight-500 mb-10 text-capitalize">
          ¡Bienvenido a la empresa: {{ $proyectoNombre ?? '—' }}!
          <div class="weight-600 font-30 text-blue">{{ trim($nombre.' '.$apellido) }}</div>
        </h4>
        <p class="font-18 max-width-600">
          ¡Bienvenido! Estamos completamente disponibles para gestionar y supervisar tus aportes,
          registrar movimientos y mantener un control organizado de tus transacciones financieras.
        </p>
      </div>

      <div class="col-md-4 text-center" style="padding:1em 0;">
        <h3 class="mb-2" style="text-decoration:none;">
          <span style="color:gray;">Hora actual en</span><br />Colombia
        </h3>
        <iframe
          src="https://www.zeitverschiebung.net/clock-widget-iframe-v2?language=es&size=medium&timezone=America%2FBogota"
          width="100%" height="115" frameborder="0" seamless>
        </iframe>
      </div>
    </div>
  </div>

  {{-- Selector oculto de proyecto (si lo necesitas) --}}
  <div class="col-sm-12 col-md-10 d-none">
    <form method="post" action="#">
      @csrf
      <select name="proyecto" class="custom-select col-12" onchange="this.form.submit()">
        <option value="">Seleccione</option>
        @foreach ($proyectos as $p)
          <option value="{{ $p->ID_Proyecto }}" {{ $p->ID_Proyecto == $proyectoSeleccionado ? 'selected' : '' }}>
            {{ $p->Nombre }}
          </option>
        @endforeach
      </select>
    </form>
  </div>

  {{-- Gráficas principales --}}
  <div class="bg-white pd-20 card-box mb-30">
    <h5 class="h4 text-blue mb-20">Gráfica circular %</h5>
    <div id="chart6"></div>
  </div>

  <div class="bg-white pd-20 card-box mb-30">
    <h5 class="h4 text-blue mb-20">Gráfica de barras %</h5>
    <div id="barras"></div>
  </div>

  <div class="bg-white pd-20 card-box mb-30">
   <h5 class="h4 text-blue mb-20">Inversiones realizadas expresadas en millones</h5>
    <div id="TimeLine"></div>
  </div>

  <div class="row clearfix">
    <div class="col-lg-6 col-md-12 col-sm-12 mb-30">
      <div class="pd-20 card-box" style="height: 360px;">
        <h5 class="h4 text-blue mb-20">% Aportes</h5>
        <div class="pd-20 card-box height-100-p" style="height: 255px">
          <div id="chart8Valor" style="max-width: 500px;"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-md-12 col-sm-12 mb-30">
      <div class="pd-20 card-box" style="height: 360px; overflow: hidden;">
        <h5 class="h4 text-blue mb-20">Usuarios</h5>
        <div class="pd-20 card-box height-100-p" style="height: 255px">
          <div id="velocimetro"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-md-12 col-sm-12 mb-30">
      <div class="pd-20 card-box" style="height: 360px; overflow: hidden;">
        <h5 class="h4 text-blue mb-20">Gráfica de radar % tipos de inversiones</h5>
        <div class="pd-20 card-box height-100-p" style="height: 255px, overflow: hidden">
          <div id="chart8Tipos"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-md-12 col-sm-12 mb-30">
      <div class="pd-20 card-box" style="height: 360px;">
        <h5 class="h4 text-blue mb-20">Participación máxima vs mínima</h5>
        <div class="pd-20 card-box height-100-p" style="height: 255px">
          <div id="chart9" style="max-width: 500px;"></div>
        </div>
      </div>
    </div>

    {{-- KPIs cards --}}
    <div class="col-md-4 col-sm-12 mb-30">
      <div class="card text-white bg-primary card-box">
        <div class="card-body">
          <h5 class="card-title text-white">Participación Mínima</h5>
          <p id="min" class="card-text">{{ number_format($participacionMin, 2, ',', '.') }}%</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-12 mb-30">
      <div class="card text-white bg-success card-box">
        <div class="card-body">
          <h5 class="card-title text-white">Participación Máxima</h5>
          <p id="max" class="card-text">{{ number_format($participacionMax, 2, ',', '.') }}%</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-12 mb-30">
      <div class="card text-white bg-primary card-box">
        <div class="card-body">
          <h5 class="card-title text-white">Promedio Participación</h5>
          <p class="card-text">{{ number_format($promedioParticipacion, 2, ',', '.') }}%</p>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-sm-12 mb-30">
      <div class="card text-white bg-primary card-box">
        <div class="card-body">
          <h5 class="card-title text-white">Valor de los aportes de capital</h5>
          <p class="card-text">$ {{ number_format($valorCapital, 0, ',', '.') }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-12 mb-30">
      <div class="card text-white bg-primary card-box">
        <div class="card-body">
          <h5 class="card-title text-white">Valor de los aportes de industria</h5>
          <p class="card-text">$ {{ number_format($valorIndustria, 0, ',', '.') }}</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabla resumen por socio --}}
  @if ($proyectoNombre && count($inversionesPorUsuario))
  <div class="card-box pb-10">
    <div class="h5 pd-20 mb-0">Resumen</div>
    <table class="table hover nowrap">
      <thead>
        <tr>
          <th>Socios</th>
          <th>Aportes de capital</th>
          <th>Aportes de industria</th>
          <th>Total aportes</th>
          <th>Porcentaje participación</th>
        </tr>
      </thead>
      <tbody id="data-table-body">
        @php
          $tCapital = 0; $tIndustria = 0; $tTotal = 0;
        @endphp
        @foreach ($inversionesPorUsuario as $datos)
          @php
            $totalU = $datos['Capital'] + $datos['Industria'];
            $tCapital += $datos['Capital'];
            $tIndustria += $datos['Industria'];
            $tTotal += $totalU;
          @endphp
          <tr>
            <td>{{ $datos['Nombre'] }}</td>
            <td class="text-end">$ {{ number_format($datos['Capital'], 0, ',', '.') }}</td>
            <td class="text-end">$ {{ number_format($datos['Industria'], 0, ',', '.') }}</td>
            <td class="text-end">$ {{ number_format($totalU, 0, ',', '.') }}</td>
            <td class="text-center">{{ number_format($datos['Porcentaje'], 2, ',', '.') }}%</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <th>TOTALES</th>
          <th class="text-end" id="valor-capital">$ {{ number_format($tCapital, 0, ',', '.') }}</th>
          <th class="text-end" id="valor-industria">$ {{ number_format($tIndustria, 0, ',', '.') }}</th>
          <th class="text-end" id="Valor-Total">$ {{ number_format($tTotal, 0, ',', '.') }}</th>
          <th class="text-center">100%</th>
        </tr>
      </tfoot>
    </table>
  </div>
  @endif

</div>

{{-- ===== Pasar datos al front (como hacías con localStorage y JS) ===== --}}
<script>
  // KPIs a localStorage (si tus scripts los leen así)
  localStorage.setItem('cantidadUsuarios', {{ (int)$cantidadUsuarios }});
  localStorage.setItem('inversionTipo1', {{ (float)$montoTipo1 }});
  localStorage.setItem('inversionTipo2', {{ (float)$montoTipo2 }});
  localStorage.setItem('inversionTipo3', {{ (float)$montoTipo3 }});
  // Datos mensuales para gráficas
  window.datosMensuales = @json($datosMensuales);
  // (Opcional) tasa usada en VF:
  window.tasaAjustada = {{ (float)$tasaAjustada }};
</script>

{{-- ===== Librerías que usabas (puedes servirlas por Vite o CDN) ===== --}}
{{-- Ejemplos de CDN (ajusta rutas si ya los empacas con Vite) --}}
<script src="{{ asset('src/plugins/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('vendors/scripts/apexcharts-setting-simulacion.js') }}"></script>

<script src="{{ asset('src/plugins/highcharts-6.0.7/code/highcharts.js') }}"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/10/highcharts.js"></script>
<script src="{{ asset('vendors/scripts/highchart-setting-simulacion.js') }}"></script>

{{-- DataTables (si lo necesitas aquí) --}}
<script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/buttons.print.min.js') }}"></script }}
<script src="{{ asset('src/plugins/datatables/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/pdfmake.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/vfs_fonts.js') }}"></script>
<script src="{{ asset('vendors/scripts/datatable-setting.js') }}"></script>
@endsection
