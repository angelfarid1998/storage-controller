@extends('layouts.app')

@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>👋 Bienvenido, {{ $user->name }}</h2>
    <span class="badge bg-primary text-uppercase">{{ $user->role->name }}</span>
  </div>

  {{-- Estadísticas --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm text-center p-3">
        <h6>Usuarios registrados</h6>
        <h2 class="text-primary">{{ $stats['usuarios'] }}</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm text-center p-3">
        <h6>Grupos activos</h6>
        <h2 class="text-success">{{ $stats['grupos'] }}</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm text-center p-3">
        <h6>Archivos cargados</h6>
        <h2 class="text-danger">{{ $stats['archivos'] }}</h2>
      </div>
    </div>
  </div>

  {{-- almacenamiento por grupo --}}
  <div class="card shadow-sm p-4 mt-4">
    <h4 class="mb-3">💾 Uso total de almacenamiento por grupo</h4>
    <canvas id="storageChart" height="120"></canvas>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
  const ctx = document.getElementById('storageChart');
  const data = {
    labels: {!! json_encode($groupData->pluck('group')) !!},
    datasets: [{
      label: 'Uso de almacenamiento (MB)',
      data: {!! json_encode($groupData->pluck('storage')) !!},
      backgroundColor: 'rgba(13, 110, 253, 0.5)',
      borderColor: 'rgba(13, 110, 253, 1)',
      borderWidth: 1
    }]
  };

  new Chart(ctx, {
    type: 'bar',
    data: data,
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (context) => `${context.formattedValue} MB`
          }
        }
      }
    }
  });
});
</script>
@endsection