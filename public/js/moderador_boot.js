(function () {
  var el = document.getElementById('js-bootstrap');
  if (!el) return;

  function num(attr) {
    var v = el.getAttribute(attr);
    return Number(v ? v : 0);
  }
  function str(attr) {
    var v = el.getAttribute(attr);
    return v ? v : '';
  }

  // KPIs -> localStorage
  try {
    localStorage.setItem('cantidadUsuarios', String(num('data-cantidad-usuarios')));
    localStorage.setItem('inversionTipo1',   String(num('data-monto1')));
    localStorage.setItem('inversionTipo2',   String(num('data-monto2')));
    localStorage.setItem('inversionTipo3',   String(num('data-monto3')));
  } catch(e) {}

  // Datos mensuales y tasa (para tus scripts de charts)
  var dm = str('data-datos-mensuales');
  try {
    window.datosMensuales = JSON.parse(dm && dm.length ? dm : '[]');
  } catch (e) {
    window.datosMensuales = [];
  }
  window.tasaAjustada = num('data-tasa-ajustada');
})();
