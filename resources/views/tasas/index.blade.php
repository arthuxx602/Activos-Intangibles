@extends('layouts.app')

@section('title','Tasas')
@section('page-title','Tasas de interés')

@section('content')
<div class="card">
  <div class="card-body">
    <form id="tasasForm" onsubmit="event.preventDefault(); validarGuardar();">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Tasa Libre de Riesgo EEUU</label>
          <input id="tlr" type="number" class="form-control" placeholder="%">
          <input id="tlr2" class="form-control mt-1" readonly placeholder="%">
        </div>
        <div class="col-md-6">
          <label class="form-label">Tasa libre de riesgo Colombia</label>
          <input id="tlrc" type="number" class="form-control" placeholder="%">
          <input id="tlrc2" class="form-control mt-1" readonly placeholder="%">
        </div>
        <div class="col-md-6">
          <label class="form-label">Retorno S&P 500</label>
          <input id="rsyp" type="number" class="form-control" placeholder="%">
          <input id="rsyp2" class="form-control mt-1" readonly placeholder="%">
        </div>
        <div class="col-md-6">
          <label class="form-label">Beta Desapalancado</label>
          <input id="bd" type="number" class="form-control">
          <input id="bd2" class="form-control mt-1" readonly>
        </div>

        <div class="col-md-4">
          <label class="form-label">Tasa de impuestos</label>
          <input id="tdi" type="number" class="form-control" placeholder="%">
          <input id="tdi2" class="form-control mt-1" readonly placeholder="%">
        </div>
        <div class="col-md-4">
          <label class="form-label">Activo</label>
          <input id="a" type="number" class="form-control">
          <input id="a2" class="form-control mt-1" readonly>
        </div>
        <div class="col-md-4">
          <label class="form-label">Deuda</label>
          <input id="d" type="number" class="form-control">
          <input id="d2" class="form-control mt-1" readonly>
        </div>

        <div class="col-md-4">
          <label class="form-label">Patrimonio</label>
          <input id="p" type="number" class="form-control">
          <input id="p2" class="form-control mt-1" readonly>
        </div>
        <div class="col-md-4">
          <label class="form-label">Devaluación esperada</label>
          <input id="de" type="number" class="form-control" placeholder="%">
          <input id="de2" class="form-control mt-1" readonly placeholder="%">
        </div>
        <div class="col-md-4">
          <label class="form-label">Prima por tamaño</label>
          <input id="ppt" type="number" class="form-control" placeholder="%">
          <input id="ppt2" class="form-control mt-1" readonly placeholder="%">
        </div>

        {{-- Resultados claves --}}
        <div class="col-md-4">
          <label class="form-label">Tasa descuento ajustada (tddaar)</label>
          <input id="tddaar2" class="form-control" readonly placeholder="%">
        </div>
        <div class="col-md-4">
          <label class="form-label">Costo deuda después impuestos</label>
          <input id="cdddi2" class="form-control" readonly placeholder="%">
        </div>
        <div class="col-md-4">
          <label class="form-label">Costo exigido en pesos (Ke)</label>
          <input id="cepeiep2" class="form-control" readonly placeholder="%">
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-outline-secondary" onclick="cargarLocal()">Cargar últimos</button>
        <button type="button" class="btn btn-outline-danger" onclick="localStorage.clear();showToast('Datos locales borrados','warning')">Borrar locales</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const API='/api';

function pct(x){ return (parseFloat(x)||0)/100; }
function validarGuardar(){
  calcular();
  guardarLocal();
  guardarServidor();
}
function calcular(){
  const tlr2=pct(tlr.value), tlrc2=pct(tlrc.value), rsyp2=pct(rsyp.value),
        bd2=parseFloat(bd.value)||0, tdi2=pct(tdi.value),
        a2=parseFloat(a.value)||0, d2=parseFloat(d.value)||0, p2=parseFloat(p.value)||0,
        de2=pct(de.value), ppt2=pct(ppt.value);

  const rp2 = tlrc2 - tlr2;
  const rlr2 = tlrc2;
  const pdm2 = rsyp2 - tlrc2;
  const da2 = a2? d2/a2 : 0;
  const pa2 = a2? p2/a2 : 0;
  const dp2 = p2? d2/p2 : 0;
  const ba2 = bd2 * (1 + (dp2 * (1 - tdi2)));
  const cepi2 = tlr2 + ba2 * pdm2 + rp2;
  const cepiep2 = (1 + cepi2) * (1 + de2) - 1;
  const cepeiep2 = cepiep2 + ppt2;
  const cdddi2 = (pct(document.getElementById('cdadi')?.value||0)) * (1 - tdi2); // si no se usa cdadi input, queda 0
  const tdd2 = pa2 * cepeiep2 + da2 * cdddi2;
  const tddaar = tdd2 + pct(document.getElementById('sda')?.value||0);

  tlr2.value = (tlr2*100).toFixed(2)+'%';
  tlrc2.value = (tlrc2*100).toFixed(2)+'%';
  rsyp2.value = (rsyp2*100).toFixed(2)+'%';
  bd2.value = bd2.toFixed(2);
  tdi2.value = (tdi2*100).toFixed(2)+'%';
  a2.value = a2.toFixed(0);
  d2.value = d2.toFixed(0);
  p2.value = p2.toFixed(0);
  de2.value = (de2*100).toFixed(2)+'%';
  ppt2.value= (ppt2*100).toFixed(2)+'%';
  document.getElementById('cepeiep2').value=(cepeiep2*100).toFixed(2)+'%';
  document.getElementById('cdddi2').value=(cdddi2*100).toFixed(2)+'%';
  tddaar2.value=(tddaar*100).toFixed(2)+'%';
  showToast('Cálculo actualizado','info');
}
function guardarLocal(){
  const ids=['tlr','tlrc','rsyp','bd','tdi','a','d','p','de','ppt','tddaar2'];
  ids.forEach(id=>localStorage.setItem('tasas:'+id, document.getElementById(id)?.value??''));
}
function cargarLocal(){
  const ids=['tlr','tlrc','rsyp','bd','tdi','a','d','p','de','ppt'];
  ids.forEach(id=>{
    const v=localStorage.getItem('tasas:'+id);
    if(v!==null) document.getElementById(id).value=v;
  });
  calcular();
}
async function guardarServidor(){
  // Guardamos SOLO la tasa ajustada (como en tu legacy)
  const tddaar = tddaar2.value.replace('%','');
  try{
    const r = await fetch(`${API}/tasas`,{
      method:'POST',
      headers:{'Accept':'application/json'},
      body: (()=>{ const fd=new FormData(); fd.append('Tasa',tddaar); return fd; })()
    });
    if(!r.ok) return toastWarn('Guardado local; no se pudo registrar en servidor');
    toastSuccess('Tasa guardada en servidor');
  }catch(e){ toastWarn('Guardado local; error de red'); }
}
document.addEventListener('DOMContentLoaded',()=>cargarLocal());
</script>
@endpush
