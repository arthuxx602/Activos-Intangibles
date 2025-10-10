@extends('layouts.app')
@section('title','Empresas')
@section('page-title','Empresas')
@section('content')
<div class="alert alert-info">Página de liquidacion (usa /api/proyectos para datos). Puedes reutilizar la tabla que ya tenías.</div>
@endsection('content')
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Empresas / Proyectos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons (opcional) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background:#f8fafc; }
    .card { border-radius: 16px; }
    .table thead th { white-space:nowrap; }
    .cursor-pointer { cursor:pointer; }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex align-items-center mb-3">
    <h1 class="h3 mb-0">Empresas (Proyectos)</h1>
    <div class="ms-auto d-flex gap-2">
      <button class="btn btn-primary" id="btnNuevo"><i class="bi bi-plus-circle"></i> Nuevo</button>
      <button class="btn btn-outline-secondary" id="btnVincular"><i class="bi bi-link-45deg"></i> Vincular usuarios</button>
    </div>
  </div>

  <!-- Filtros -->
  <div class="card mb-3">
    <div class="card-body">
      <form id="formFiltros" class="row g-2 align-items-end">
        <div class="col-sm-6 col-md-3">
          <label class="form-label">Buscar por nombre</label>
          <input type="text" class="form-control" name="search" placeholder="Ej: Lavado de carros">
        </div>
        <div class="col-sm-6 col-md-2">
          <label class="form-label">Desde</label>
          <input type="date" class="form-control" name="from">
        </div>
        <div class="col-sm-6 col-md-2">
          <label class="form-label">Hasta</label>
          <input type="date" class="form-control" name="to">
        </div>
        <div class="col-sm-6 col-md-2">
          <label class="form-label">Ordenar por</label>
          <select class="form-select" name="order_by">
            <option value="ID_Proyecto">ID</option>
            <option value="Nombre">Nombre</option>
            <option value="Fecha">Fecha</option>
          </select>
        </div>
        <div class="col-sm-6 col-md-2">
          <label class="form-label">Dirección</label>
          <select class="form-select" name="order_dir">
            <option value="desc">Desc</option>
            <option value="asc">Asc</option>
          </select>
        </div>
        <div class="col-sm-6 col-md-1">
          <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" value="1" id="soloNoLiquidados" name="solo_no_liquidados">
            <label class="form-check-label" for="soloNoLiquidados">No liquidados</label>
          </div>
        </div>
        <div class="col-12 col-md-12 d-flex gap-2 mt-2">
          <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Buscar</button>
          <button class="btn btn-outline-secondary" type="button" id="btnLimpiar"><i class="bi bi-x-circle"></i> Limpiar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle" id="tabla">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Fecha</th>
              <th>Descripción</th>
              <th>Certificado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
      <nav>
        <ul class="pagination justify-content-end mb-0" id="paginacion"></ul>
      </nav>
    </div>
  </div>
</div>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalProyecto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form class="modal-content" id="formProyecto">
      <div class="modal-header">
        <h5 class="modal-title" id="titleProyecto">Nuevo proyecto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="_id" id="_id">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="Nombre" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <input type="date" class="form-control" name="Fecha">
          </div>
          <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="Descripcion" rows="3"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Certificado (pdf/zip/jpg/png)</label>
            <input type="file" class="form-control" name="Certificado" accept=".pdf,.zip,.jpg,.jpeg,.png">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Vinculación -->
<div class="modal fade" id="modalVincular" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form class="modal-content" id="formVincular">
      <div class="modal-header">
        <h5 class="modal-title">Vincular usuarios a empresa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Empresa (proyecto)</label>
            <select class="form-select" name="proyecto" id="selectProyecto" required></select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Usuarios</label>
            <select multiple class="form-select" name="usuarios[]" id="selectUsuarios" size="8" required></select>
            <div class="form-text">Ctrl/Cmd + click para selección múltiple.</div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar vínculos</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap JS + Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // ===== Ajusta tu base de API si usas otro puerto/origen
  const API_BASE = '/api';

  // ===== Helpers UI
  const $ = (sel) => document.querySelector(sel);
  const $$ = (sel) => document.querySelectorAll(sel);
  const toast = (msg, type='primary') => {
    const el = document.createElement('div');
    el.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    el.style.zIndex = 1080;
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(()=> el.remove(), 2500);
  };

  // ===== Estado y carga
  let currentPageUrl = null; // para paginación

  async function loadProyectos(url=null) {
    const params = new URLSearchParams(new FormData($('#formFiltros')));
    // Checkbox -> boolean
    if (!$('#soloNoLiquidados').checked) params.delete('solo_no_liquidados');

    const endpoint = url || `${API_BASE}/proyectos?${params.toString()}`;
    currentPageUrl = endpoint;

    const res = await fetch(endpoint);
    if (!res.ok) return toast('Error cargando proyectos', 'danger');
    const data = await res.json();
    renderTable(data);
    renderPagination(data);
  }

  function renderTable(data) {
    const tbody = $('#tbody');
    tbody.innerHTML = '';
    (data.data || []).forEach(p => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${p.ID_Proyecto}</td>
        <td>${p.Nombre ?? ''}</td>
        <td>${p.Fecha ?? ''}</td>
        <td>${p.Descripcion ?? ''}</td>
        <td>
          ${p.Certificado ? `
            <a class="btn btn-sm btn-outline-secondary" href="${API_BASE}/descargas/proyectos/${p.ID_Proyecto}/certificado" target="_blank">
              <i class="bi bi-download"></i> Descargar
            </a>` : '<span class="text-muted">—</span>'}
        </td>
        <td class="text-end">
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-primary" onclick='openEdit(${JSON.stringify(p)})'>
              <i class="bi bi-pencil-square"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="eliminar(${p.ID_Proyecto})">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </td>`;
      tbody.appendChild(tr);
    });
  }

  function renderPagination(data) {
    const ul = $('#paginacion');
    ul.innerHTML = '';
    const { prev_page_url, next_page_url, current_page, last_page } = data;

    const addItem = (label, url, disabled=false, active=false) => {
      const li = document.createElement('li');
      li.className = `page-item ${disabled ? 'disabled':''} ${active?'active':''}`;
      const a = document.createElement('a');
      a.className = 'page-link cursor-pointer';
      a.textContent = label;
      if (url) a.addEventListener('click', () => loadProyectos(url));
      li.appendChild(a);
      ul.appendChild(li);
    };

    addItem('«', prev_page_url, !prev_page_url);
    // Pocos botones por simpleza
    addItem(`${current_page}`, null, false, true);
    if (current_page < last_page) addItem(`${current_page+1}`, `${currentPageUrl.split('?')[0]}?page=${current_page+1}`);
    addItem('»', next_page_url, !next_page_url);
  }

  // ===== Crear / Editar
  const modalProyecto = new bootstrap.Modal('#modalProyecto');

  $('#btnNuevo').addEventListener('click', () => {
    $('#titleProyecto').textContent = 'Nuevo proyecto';
    $('#_id').value = '';
    $('#formProyecto').reset();
    modalProyecto.show();
  });

  function openEdit(p) {
    $('#titleProyecto').textContent = 'Editar proyecto';
    $('#_id').value = p.ID_Proyecto;
    $('#formProyecto').Nombre.value = p.Nombre || '';
    $('#formProyecto').Fecha.value = p.Fecha || '';
    $('#formProyecto').Descripcion.value = p.Descripcion || '';
    $('#formProyecto').Certificado.value = ''; // limpiar file
    modalProyecto.show();
  }

  $('#formProyecto').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = $('#_id').value;
    const form = new FormData(e.target);
    const hasFile = !!form.get('Certificado')?.name;

    let res;
    if (id) {
      // PUT multipart (algunos servidores requieren POST + _method=PUT)
      form.append('_method','PUT');
      res = await fetch(`${API_BASE}/proyectos/${id}`, { method:'POST', body: form });
    } else {
      res = await fetch(`${API_BASE}/proyectos`, { method:'POST', body: form });
    }

    if (!res.ok) {
      const err = await safeJson(res);
      toast(err?.message || 'Error guardando', 'danger');
      return;
    }

    toast('Guardado con éxito');
    modalProyecto.hide();
    loadProyectos();
  });

  async function eliminar(id) {
    if (!confirm('¿Eliminar este proyecto?')) return;
    const res = await fetch(`${API_BASE}/proyectos/${id}`, { method:'DELETE' });
    if (res.status === 409) {
      const err = await safeJson(res);
      toast(err?.message || 'No se puede eliminar porque tiene vínculos.', 'warning');
      return;
    }
    if (!res.ok) {
      toast('Error eliminando', 'danger');
      return;
    }
    toast('Eliminado');
    loadProyectos();
  }

  // ===== Vincular usuarios
  const modalVincular = new bootstrap.Modal('#modalVincular');
  $('#btnVincular').addEventListener('click', async () => {
    await Promise.all([loadProyectosCatalogo(), loadUsuariosCatalogo()]);
    modalVincular.show();
  });

  async function loadProyectosCatalogo() {
    const res = await fetch(`${API_BASE}/catalogos/proyectos-no-liquidados`);
    const data = await res.json();
    const sel = $('#selectProyecto');
    sel.innerHTML = '<option value="">Seleccione…</option>';
    (data || []).forEach(p => {
      const opt = document.createElement('option');
      opt.value = p.ID_Proyecto;
      opt.textContent = `${p.Nombre} (${p.ID_Proyecto})`;
      sel.appendChild(opt);
    });
  }

  async function loadUsuariosCatalogo() {
    const res = await fetch(`${API_BASE}/catalogos/usuarios-para-vincular`);
    const data = await res.json();
    const sel = $('#selectUsuarios');
    sel.innerHTML = '';
    (data || []).forEach(u => {
      const opt = document.createElement('option');
      opt.value = u.ID_Usuario;
      opt.textContent = `Céd: ${u.ID_Usuario} — ${u.Nombre} ${u.Apellido}`;
      sel.appendChild(opt);
    });
  }

  $('#formVincular').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const proyecto = fd.get('proyecto');
    const usuarios = Array.from($('#selectUsuarios').selectedOptions).map(o => Number(o.value));

    if (!proyecto || usuarios.length === 0) {
      toast('Seleccione proyecto y al menos un usuario', 'warning');
      return;
    }

    const res = await fetch(`${API_BASE}/vinculaciones`, {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify({ proyecto: Number(proyecto), usuarios })
    });

    if (!res.ok) {
      const err = await safeJson(res);
      toast(err?.message || 'Error vinculando', 'danger');
      return;
    }

    toast('Vínculos guardados');
    modalVincular.hide();
  });

  // ===== Utils
  async function safeJson(res) {
    try { return await res.json(); } catch { return null; }
  }

  // ===== Eventos filtros
  $('#formFiltros').addEventListener('submit', (e) => { e.preventDefault(); loadProyectos(); });
  $('#btnLimpiar').addEventListener('click', () => {
    $('#formFiltros').reset();
    loadProyectos();
  });

  // ===== Init
  loadProyectos();
</script>
</body>
</html>
@endsection