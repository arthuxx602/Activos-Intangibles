@extends('layouts.moderador')
@section('title','Consultas')

@section('content')
<div class="main-container">
  <div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">

      {{-- Formulario de selección --}}
      <div class="page-header">
        <form method="POST" action="{{ route('moderador.consultas.consultar') }}">
          @csrf
          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label class="form-label">Usuarios</label>
              <select name="usuario" id="usuario" class="form-select" onchange="cargarProyectos()" required>
                <option value="" selected>Seleccione</option>
                @foreach ($usuarios as $u)
                  <option value="{{ $u->ID_Usuario }}"
                          @if(optional($result)['usuarioId'] ?? null == $u->ID_Usuario) selected @endif>
                    Cédula: {{ $u->ID_Usuario }} — {{ $u->Nombre }} {{ $u->Apellido }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Proyecto</label>
              <select name="proyecto" id="proyecto" class="form-select" required>
                <option value="" selected>Seleccione un usuario</option>
                {{-- Se llena por AJAX --}}
              </select>
            </div>

            <div class="col-12">
              <button class="btn btn-primary" type="submit" name="consultar" value="1">Consultar</button>
            </div>
          </div>
        </form>
      </div>

      {{-- KPIs --}}
      @php
        $hasResult = isset($result) && is_array($result);
      @endphp
      <div class="row pb-10 mt-3">
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
          <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
              <div class="widget-data">
                <div class="weight-700 font-24 text-dark">{{ $hasResult ? $result['numInversiones'] : 0 }}</div>
                <div class="font-14 text-secondary weight-500">Inversiones Realizadas</div>
              </div>
              <div class="widget-icon"><div class="icon" data-color="#00eccf"><i class="icon-copy dw dw-calendar1"></i></div></div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
          <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
              <div class="widget-data">
                <div class="weight-700 font-24 text-dark">{{ $hasResult ? $result['vidaDias'] : 0 }} días</div>
                <div class="font-14 text-secondary weight-500">Vida del proyecto</div>
              </div>
              <div class="widget-icon"><div class="icon" data-color="#ff5b5b"><span class="icon-copy ti-heart"></span></div></div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
          <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
              <div class="widget-data">
                <div class="weight-700 font-24 text-dark">{{ $hasResult ? $result['numInversionistas'] : 0 }}</div>
                <div class="font-14 text-secondary weight-500">Inversionistas</div>
              </div>
              <div class="widget-icon"><div class="icon"><i class="icon-copy bi bi-globe" aria-hidden="true"></i></div></div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
          <div class="card-box height-100-p widget-style3">
            <div class="d-flex flex-wrap">
              <div class="widget-data">
                <div class="weight-700 font-24 text-dark">{{ number_format($tasaAjustada, 2, ',', '.') }}%</div>
                <div class="font-14 text-secondary weight-500">Tasa ajustada</div>
              </div>
              <div class="widget-icon"><div class="icon" data-color="#09cc06"><i class="icon-copy fa fa-money" aria-hidden="true"></i></div></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Resumen inversiones (capital, industria, total) --}}
      <div class="row clearfix">
        <div class="col-lg-6 col-md-12 col-sm-12 mb-30">
          <div class="pd-20 card-box">
            <h5 class="h4 text-blue mb-20">Resumen Inversiones</h5>
            <div class="card-box pd-20 mb-20" style="max-width: 520px; min-height: 250px;">
              <table class="table">
                <tbody>
                  <tr>
                    <th scope="col">Valor de los aportes de capital</th>
                    <th id="valor-capital" scope="col">
                      {{ $hasResult ? number_format($result['valorCapital'], 0, ',', '.') : 0 }}
                    </th>
                  </tr>
                  <tr>
                    <th scope="col">Valor de los aportes de industria</th>
                    <th id="valor-industria" scope="col">
                      {{ $hasResult ? number_format($result['valorIndustria'], 0, ',', '.') : 0 }}
                    </th>
                  </tr>
                  <tr>
                    <th scope="col">Total Aportes</th>
                    <th id="suma-capital-industria" scope="col">
                      {{ $hasResult ? number_format($result['totalAportes'], 0, ',', '.') : 0 }}
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Placeholder del gráfico (si lo usas aquí) --}}
        <div class="col-lg-6 col-md-12 col-sm-12 mb-30">
          <div class="pd-20 card-box" style="height: 360px;">
            <h5 class="h4 text-blue mb-20">Gráfico tipo de aporte</h5>
            <div class="pd-20 card-box height-100-p" style="height: 255px">
              <div id="chart8Valor" style="max-width: 500px;"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Tabla: Aporte en Dinero --}}
      <div class="pd-20 card-box mb-30">
        <div class="card-box pb-10">
          <div class="h5 pd-20 mb-0">Aporte en Dinero</div>
          <table class="table nowrap">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Valor del Aporte ajustado</th>
                <th>Tiempo transcurrido</th>
                <th>Total aporte en dinero (ajustado)</th>
              </tr>
            </thead>
            <tbody>
              @php $din = $hasResult ? $result['tablas']['dinero'] : []; @endphp
              @forelse ($din as $fila)
                <tr>
                  <td>{{ $fila['Fecha'] }}</td>
                  <td>{{ number_format($fila['Monto'], 0, ',', '.') }}</td>
                  <td>{{ number_format($fila['VF'], 0, ',', '.') }}</td>
                  <td>{{ $fila['Dias'] }} días</td>
                  <td>{{ number_format($fila['Acumulado'], 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr><td colspan="5">No se encontraron resultados.</td></tr>
              @endforelse
            </tbody>
            @if($hasResult)
              <tfoot>
                <tr>
                  <th>Total aporte en dinero (ajustado)</th>
                  <th colspan="4">{{ number_format($result['totalesVF']['dinero'], 0, ',', '.') }}</th>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>
      </div>

      {{-- Tabla: Aportes en especie --}}
      <div class="pd-20 card-box mb-30">
        <div class="card-box pb-10">
          <div class="h5 pd-20 mb-0">Aportes en especie</div>
          <table class="table nowrap">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Valor del Aporte ajustado</th>
                <th>Tiempo transcurrido</th>
              </tr>
            </thead>
            <tbody>
              @php $esp = $hasResult ? $result['tablas']['especie'] : []; @endphp
              @forelse ($esp as $fila)
                <tr>
                  <td>{{ $fila['Fecha'] }}</td>
                  <td>{{ number_format($fila['Monto'], 0, ',', '.') }}</td>
                  <td>{{ number_format($fila['VF'], 0, ',', '.') }}</td>
                  <td>{{ $fila['Dias'] }} días</td>
                </tr>
              @empty
                <tr><td colspan="4">No se encontraron resultados.</td></tr>
              @endforelse
            </tbody>
            @if($hasResult)
              <tfoot>
                <tr>
                  <th>Total aporte en especie (ajustado)</th>
                  <th colspan="3">{{ number_format($result['totalesVF']['especie'], 0, ',', '.') }}</th>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>
      </div>

      {{-- Tabla: Aportes en industria --}}
      <div class="pd-20 card-box mb-30">
        <div class="card-box pb-10">
          <div class="h5 pd-20 mb-0">Aportes en industria</div>
          <table class="table nowrap">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Valor del Aporte ajustado</th>
                <th>Tiempo transcurrido</th>
              </tr>
            </thead>
            <tbody>
              @php $ind = $hasResult ? $result['tablas']['industria'] : []; @endphp
              @forelse ($ind as $fila)
                <tr>
                  <td>{{ $fila['Fecha'] }}</td>
                  <td>{{ number_format($fila['Monto'], 0, ',', '.') }}</td>
                  <td>{{ number_format($fila['VF'], 0, ',', '.') }}</td>
                  <td>{{ $fila['Dias'] }} días</td>
                </tr>
              @empty
                <tr><td colspan="4">No se encontraron resultados.</td></tr>
              @endforelse
            </tbody>
            @if($hasResult)
              <tfoot>
                <tr>
                  <th>Total aporte en industria (ajustado)</th>
                  <th colspan="3">{{ number_format($result['totalesVF']['industria'], 0, ',', '.') }}</th>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Carga proyectos vía AJAX al cambiar usuario --}}
<script>
  async function cargarProyectos() {
    const usuarioId = document.getElementById('usuario').value;
    const sel = document.getElementById('proyecto');
    if (!usuarioId) {
      sel.innerHTML = '<option value="">Seleccione un usuario</option>';
      return;
    }
    const url = new URL('{{ route('moderador.consultas.proyectos-usuario') }}', window.location.origin);
    url.searchParams.set('usuario_id', usuarioId);

    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
    const html = await res.text();
    sel.innerHTML = html;

    // Si vienes de un POST con proyecto ya elegido, podríamos seleccionar aquí con JS si quieres.
  }

  // Si en la respuesta POST ya hay usuario elegido, recarga proyectos al entrar:
  @if($hasResult && $result['usuarioId'])
    document.addEventListener('DOMContentLoaded', ()=> {
      const usuario = document.getElementById('usuario');
      if (usuario.value) cargarProyectos();
    });
  @endif
</script>
@endsection
