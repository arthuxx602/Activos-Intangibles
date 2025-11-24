@extends('layouts.app') {{-- o el layout que uses --}}

@section('title', 'Elegir proyecto')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Seleccione un proyecto</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('proyectos.seleccionar.post') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label for="proyecto_id" class="form-label">Proyecto</label>
            <select name="proyecto_id" id="proyecto_id" class="form-select" required>
                <option value="">-- Seleccione --</option>
                @foreach($proyectos as $p)
                    <option value="{{ $p->ID_Proyecto }}">{{ $p->Nombre }}</option>
                @endforeach
            </select>
            @error('proyecto_id') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Continuar</button>
        </div>
    </form>
</div>
@endsection
