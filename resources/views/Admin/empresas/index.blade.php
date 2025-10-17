@extends('layouts.app')

@section('title','Empresas')
@section('page-title','Empresas')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="mb-0">Listado</h5>
  <a href="#" class="btn btn-primary disabled" title="(Pendiente) Crear Empresa"><i class="bi bi-plus-lg me-1"></i> Nueva</a>
</div>

<div class="card">
  <div class="card-body">
    <div class="row g-2 mb-3">
      <div class="col-sm-4"><input id="fSearch" class="form-control" placeholder="Buscar por nombre..."></div>
      <div class="col-sm-3"><input id="fFrom" type="date" class="form-control"></div>
      <div class="col-sm-3"><input id="fTo" type="date" class="form-control"></div>
      <div class="col-sm-2 d-grid gap-2 d-md-flex justify-content-md-end">
        <button class="btn btn-outline-secondary" onclick="resetFiltros()">Limpiar</button>
        <button class="btn btn-primary" onclick="cargar()">Aplicar</button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr>
          <th>ID</th><th>Nombre</th><th>Fecha</th><th>Descripción</th><th>Certificado</th><th class="text-end">Acciones</th>
        </tr></thead>
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
@endsection

@push('scripts')
<script>
const API='/api';
let pagination={current_page:1,last_page:1};
const $=s=>document.querySelector(s);

function resetFiltros(){ fSearch.value=''; fFrom.value=''; fTo.value=''; pagination.current_page=1; cargar(); }
function cambiarPagina(d){ const n=pagination.current_page+d; if(n<1||n>pagination.last_page) return; cargar(n); }

async function cargar(page=null){
  if(page) pagination.current_page=page;
  const p=new URLSearchParams();
  if(fSearch.value) p.set('search',fSearch.value);
  if(fFrom.value)   p.set('from',fFrom.value);
  if(fTo.value)     p.set('to',fTo.value);
  p.set('page',pagination.current_page);

  try{
    const r=await fetch(`${API}/proyectos?${p.toString()}`);
    const d=await r.json();
    const rows=d.data ?? d;
    const tb=$('#tbody'); tb.innerHTML='';
    rows.forEach(x=>{
      const cert=x.Certificado?`<a target="_blank" href="${API}/descargas/proyectos/${x.ID_Proyecto}/certificado" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-arrow-down"></i></a>`:'';
      const tr=document.createElement('tr');
      tr.innerHTML=`
        <td>${x.ID_Proyecto}</td>
        <td>${x.Nombre??''}</td>
        <td>${x.Fecha??''}</td>
        <td>${x.Descripcion??''}</td>
        <td>${cert}</td>
        <td class="text-end">
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-danger" onclick="eliminar(${x.ID_Proyecto})"><i class="bi bi-trash"></i></button>
          </div>
        </td>`;
      tb.appendChild(tr);
    });

    if(d.current_page){
      pagination.current_page=d.current_page; pagination.last_page=d.last_page;
      pageInfo.textContent=`Página ${d.current_page} de ${d.last_page} • ${d.total} registros`;
      prevBtn.disabled=(d.current_page<=1); nextBtn.disabled=(d.current_page>=d.last_page);
    }else{ pageInfo.textContent=`${rows.length} registros`; prevBtn.disabled=nextBtn.disabled=true; }
  }catch(e){ toastError('No se pudo cargar empresas'); }
}

async function eliminar(id){
  if(!confirm('¿Eliminar empresa?')) return;
  const r = await fetch(`${API}/proyectos/${id}`,{method:'DELETE'});
  if(r.status===409){ const j=await r.json(); return toastWarn(j.message||'No se puede eliminar'); }
  if(!r.ok) return toastError('Error al eliminar');
  toastSuccess('Eliminado');
  cargar();
}

document.addEventListener('DOMContentLoaded',()=>cargar());
</script>
@endpush
