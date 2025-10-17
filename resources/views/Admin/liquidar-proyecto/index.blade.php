@extends('layouts.app')

@section('title','Liquidar proyecto')
@section('page-title','Liquidar proyecto')

@section('content')
<div class="card mb-3">
  <div class="card-body">
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Buscar</label>
        <input id="q" class="form-control" placeholder="Nombre del proyecto...">
      </div>
      <div class="col-md-4">
        <label class="form-label">Estado</label>
        <select id="estado" class="form-select">
          <option value="">Todos</option>
          <option value="0">No liquidados</option>
          <option value="1">Liquidados</option>
        </select>
      </div>
      <div class="col-md-4 d-flex gap-2">
        <button class="btn btn-primary" onclick="cargar()">Aplicar</button>
        <button class="btn btn-outline-secondary" onclick="resetFiltros()">Limpiar</button>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>ID</th><th>Nombre</th><th>Fecha</th><th>Liquidado</th><th>Certificado</th><th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2">
      <div id="pageInfo" class="text-secondary small"></div>
      <div class="btn-group">
        <button class="btn btn-outline-secondary btn-sm" id="prevBtn" onclick="cambiarPagina(-1)">«</button>
        <button class="btn btn-outline-secondary btn-sm" id="nextBtn" onclick="cambiarPagina(1)">»</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal subir acta --}}
<div class="modal fade" id="liqModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="liqForm" class="modal-content" onsubmit="subirActa(event)" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">Liquidar proyecto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="proyId">
        <div class="mb-2">
          <label class="form-label">Acta de liquidación (PDF/ZIP/JPG/PNG)</label>
          <input id="documento_L" name="documento_L" type="file" class="form-control" accept=".pdf,.zip,.jpg,.jpeg,.png" required>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const API='/api', $=s=>document.querySelector(s);
let modal, pagination={current_page:1,last_page:1};

function resetFiltros(){ q.value=''; estado.value=''; pagination.current_page=1; cargar(); }
function cambiarPagina(d){ const n=pagination.current_page+d; if(n<1||n>pagination.last_page)return; cargar(n); }

async function cargar(page=null){
  if(page) pagination.current_page=page;
  const p=new URLSearchParams();
  if(q.value) p.set('search', q.value);
  if(estado.value!=='') p.set('liquidado', estado.value); // tu API puede aceptar este filtro si lo agregaste
  p.set('page', pagination.current_page);

  const r = await fetch(`${API}/proyectos?${p.toString()}`);
  const d = await r.json();
  const rows = d.data ?? d;

  const tb=$('#tbody'); tb.innerHTML='';
  rows.forEach(x=>{
    const cert = x.Certificado ? `<a target="_blank" href="${API}/descargas/proyectos/${x.ID_Proyecto}/certificado" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-arrow-down"></i></a>` : '';
    const liq = x.liquidado==1 ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-warning text-dark">No</span>';
    const btn = x.liquidado==1 ? '' : `<button class="btn btn-sm btn-primary" onclick="abrir(${x.ID_Proyecto})"><i class="bi bi-upload"></i> Liquidar</button>`;
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td>${x.ID_Proyecto}</td>
      <td>${x.Nombre??''}</td>
      <td>${x.Fecha??''}</td>
      <td>${liq}</td>
      <td>${cert}</td>
      <td class="text-end">${btn}</td>`;
    tb.appendChild(tr);
  });

  if(d.current_page){
    pagination.current_page=d.current_page; pagination.last_page=d.last_page;
    pageInfo.textContent=`Página ${d.current_page} de ${d.last_page} • ${d.total} registros`;
    prevBtn.disabled=(d.current_page<=1); nextBtn.disabled=(d.current_page>=d.last_page);
  }else{
    pageInfo.textContent=`${rows.length} registros`; prevBtn.disabled=nextBtn.disabled=true;
  }
}
function abrir(id){ proyId.value=id; modal.show(); }
async function subirActa(e){
  e.preventDefault();
  const id=proyId.value;
  const fd = new FormData(liqForm);
  const r = await fetch(`${API}/proyectos/${id}/liquidar`, {method:'POST', body:fd});
  const j = await r.json().catch(()=>({}));
  if(!r.ok) return showToast(j.message||'No se pudo liquidar','danger');
  showToast('Proyecto liquidado','success'); modal.hide(); liqForm.reset(); cargar();
}
document.addEventListener('DOMContentLoaded', ()=>{ modal=new bootstrap.Modal('#liqModal'); cargar(); });
</script>
@endpush
