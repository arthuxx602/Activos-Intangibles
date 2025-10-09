<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Inicio | Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body { background: #f8fafc; font-family: 'Inter', sans-serif; }
    .card { border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .welcome-img { max-width: 100%; height: auto; border-radius: 12px; }
    .fs-3 { font-size: 1.5rem; }
  </style>
</head>

<body>
<div class="container py-4">
  <!-- HEADER -->
  <div class="row align-items-center g-4 mb-4">
    <div class="col-md-4 text-center text-md-start">
      <img class="welcome-img" src="https://dummyimage.com/480x240/eff2f7/6c757d&text=Bienvenido" alt="Bienvenido">
    </div>
    <div class="col-md-4">
      <h4 class="mb-2">Bienvenido de nuevo</h4>
      <div class="fs-4 text-primary fw-bold" id="nombreUsuario">Usuario</div>
      <p class="text-secondary mb-0">
        ¡Nos alegra verte! Estamos listos para ayudarte con todo lo relacionado con tus proyectos e inversiones.
      </p>
    </div>
    <div class="col-md-4 text-center">
      <h5 class="text-muted mb-1">Hora actual en Colombia</h5>
      <iframe
        src="https://www.zeitverschiebung.net/clock-widget-iframe-v2?language=es&size=medium&timezone=America%2FBogota"
        width="100%" height="115" frameborder="0" seamless></iframe>
    </div>
  </div>

  <!-- MÉTRICAS -->
  <div class="row g-3" id="cards">
    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="fs-3 fw-bold" id="totalProyectos">0</div>
            <div class="text-secondary">Empresas</div>
          </div>
          <div class="ms-3 text-info"><i class="bi bi-building fs-1"></i></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="text-secondary mb-1">Inicio primera empresa</div>
            <div class="fw-semibold" id="fechaMasAntigua">—</div>
          </div>
          <div class="ms-3 text-danger"><i class="bi bi-calendar2-week fs-1"></i></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="text-secondary mb-1">Inicio última empresa</div>
            <div class="fw-semibold" id="fechaMasNueva">—</div>
          </div>
          <div class="ms-3 text-warning"><i class="bi bi-calendar2-event fs-1"></i></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card">
        <div class="card-body d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="fs-3 fw-bold" id="totalUsuarios">0</div>
            <div class="text-secondary">Usuarios</div>
          </div>
          <div class="ms-3 text-success"><i class="bi bi-people fs-1"></i></div>
        </div>
      </div>
    </div>
  </div>

  <!-- GRÁFICO: Empresas por mes -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
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
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
  const API_BASE = '/api'; // Base de la API Laravel

  // Usuario simulado (puedes cambiarlo luego por datos reales de sesión/Auth)
  const nombre = localStorage.getItem('nombre') || 'Arturo';
  const apellido = localStorage.getItem('apellido') || 'Marquez';
  document.getElementById('nombreUsuario').textContent = `${nombre} ${apellido}`;

  // ===== Tarjetas (summary) =====
  async function cargarDashboard() {
    try {
      const res = await fetch(`${API_BASE}/dashboard/summary`);
      if (!res.ok) throw new Error('Error al cargar el dashboard');
      const data = await res.json();
      document.getElementById('totalProyectos').textContent = data.total_proyectos ?? 0;
      document.getElementById('fechaMasAntigua').textContent = data.fecha_mas_antigua ?? '—';
      document.getElementById('fechaMasNueva').textContent   = data.fecha_mas_nueva ?? '—';
      document.getElementById('totalUsuarios').textContent   = data.total_usuarios ?? 0;
    } catch (error) {
      console.error(error);
      alert('No se pudo cargar el dashboard.');
    }
  }

  // ===== Gráfico: Proyectos por mes =====
  let chart; // instancia ApexCharts

  function poblarAnios() {
    const sel = document.getElementById('chartYear');
    const actual = new Date().getFullYear();
    sel.innerHTML = '';
    for (let y = actual; y >= actual - 5; y--) {
      const opt = document.createElement('option');
      opt.value = y;
      opt.textContent = y;
      sel.appendChild(opt);
    }
    sel.value = actual;
  }

  async function cargarGrafico(year) {
    try {
      const res = await fetch(`${API_BASE}/dashboard/proyectos-por-mes?year=${year}`);
      if (!res.ok) throw new Error('Error al cargar series');
      const data = await res.json();

      const options = {
        chart: { type: 'bar', height: 320, toolbar: { show: false } },
        series: [{ name: `Empresas ${data.year}`, data: data.series }],
        xaxis: { categories: data.labels },
        plotOptions: { bar: { borderRadius: 6 } },
        dataLabels: { enabled: false },
        stroke: { width: 2 },
        tooltip: { y: { formatter: (val) => `${val} empresas` } },
        colors: undefined // usa colores por defecto de ApexCharts
      };

      const el = document.querySelector('#chartProyectosMes');
      if (chart) chart.destroy();
      chart = new ApexCharts(el, options);
      chart.render();
    } catch (e) {
      console.error(e);
      alert('No se pudo cargar la gráfica.');
    }
  }

  // Init
  document.addEventListener('DOMContentLoaded', () => {
    cargarDashboard();
    poblarAnios();
    const sel = document.getElementById('chartYear');
    cargarGrafico(sel.value);
    sel.addEventListener('change', (e) => cargarGrafico(e.target.value));
  });
</script>
</body>
</html>
