@extends('layouts.app')

@section('title','Distribuir participación')
@section('page-title','Distribuir participación')

@section('content')
<div class="card">
  <div class="card-body">
    <form id="liqForm" onsubmit="enviar(event)" enctype="multipart/form-data">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Proyecto</label>
          <select id="proyecto" name="proyecto" class="form-select" required></select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Acta de liquidación (PDF/ZIP/JPG/PNG)</label>
          <input type="file" id="documento_L" name="documento_L" class="form-control" accept=".pdf,.zip,.jpg,.jpeg,.png" required>
        </div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary">Liquidar</button>
        <button class="btn btn-outline-secondary" type="reset">Limpiar</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const API='/api';
async function cargarProyectos(){
  const r=await fetch(`${API}/catalogos/proyectos-no-liquidados`);
  const d=await r.json();
  proyecto.innerHTML='<option value="">Seleccione...</option>';
  (d||[]).forEach(p=> proyecto.add(new Option(`${p.ID_Proyecto} - ${p.Nombre}`, p.ID_Proyecto)));
}
async function enviar(e){
  e.preventDefault();
  if(!proyecto.value) return toastWarn('Selecciona un proyecto');
  if(!documento_L.files.length) return toastWarn('Adjunta el acta');

  const fd=new FormData(); fd.append('documento_L', documento_L.files[0]);
  try{
    const r=await fetch(`${API}/proyectos/${proyecto.value}/liquidar`,{method:'POST',body:fd});
    const j=await r.json().catch(()=>({}));
    if(!r.ok) return toastError(j.message||'No se pudo liquidar');
    toastSuccess('Proyecto liquidado');
    cargarProyectos();
  }catch(e){ toastError('Error de red'); }
}
document.addEventListener('DOMContentLoaded', cargarProyectos);
</script>
@endpush
