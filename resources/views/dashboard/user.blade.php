@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>👋 Hola, {{ $user->name }}</h2>
        <span class="badge bg-secondary text-uppercase">{{ $user->role->name }}</span>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card text-center shadow-sm p-3">
                <h6>Espacio usado</h6>
                <h3 class="text-danger">{{ number_format($stats['used'] / 1024, 2) }} KB</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm p-3">
                <h6>Cuota total</h6>
                <h3 class="text-primary">{{ number_format($stats['quota'] / 1024, 2) }} KB</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm p-3">
                <h6>Disponible</h6>
                <h3 class="text-success">{{ number_format($stats['available'] / 1024, 2) }} KB</h3>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h4>📂 Últimos archivos cargados</h4>
        <table class="table table-hover mt-3">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tamaño (KB)</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['files'] as $file)
                <tr>
                    <td>{{ $file->filename }}</td>
                    <td>{{ number_format($file->size / 1024, 2) }}</td>
                    <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Aún no has cargado archivos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection