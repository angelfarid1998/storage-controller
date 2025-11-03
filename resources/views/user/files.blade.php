@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📂 Mis Archivos</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">⬅ Volver</a>
    </div>

    {{-- Formulario de carga --}}
    <form id="uploadForm" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="file" name="file" id="fileInput" class="form-control" required>
            <button type="submit" class="btn btn-primary">📤 Subir</button>
        </div>
    </form>

    {{-- Mensaje de estado --}}
    <div id="alertBox" class="alert d-none mt-3"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered align-middle shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $file)
                    <tr id="file-row-{{ $file->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $file->filename }}</td>
                        <td>{{ number_format($file->size / 1024, 2) }} KB</td>
                        <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('files.download', $file->id) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-download"></i> Descargar</a>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                onclick="deleteFile({{ $file->id }})">
                                <i class="bi bi-trash3"></i> Eliminar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No has cargado ningún archivo todavía.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="uploadOverlay">
        <div class="spinner-border text-light mb-3" role="status"></div>
        <h5 class="fw-light">Cargando archivo, por favor espera...</h5>
    </div>
</div>

<script>
    const overlay = document.getElementById('uploadOverlay');

document.querySelector('#uploadForm').addEventListener('submit', (e) => {
  e.preventDefault();
  const file = document.querySelector('#fileInput').files[0];
  if (!file) return showToast('Selecciona un archivo primero.', 'warning');

  const formData = new FormData();
  formData.append('file', file);

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '{{ route('files.store') }}', true);
  xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

  overlay.style.display = 'flex';

  // Mostrar porcentaje de progreso
  xhr.upload.addEventListener('progress', (e) => {
    if (e.lengthComputable) {
      const percent = Math.round((e.loaded / e.total) * 100);
      overlay.querySelector('h5').textContent = `Cargando archivo... ${percent}%`;
    }
  });

  // al terminar
  xhr.onload = function() {
  overlay.style.display = 'none';

  let data = {};
  try {
    data = JSON.parse(xhr.responseText);
  } catch (e) {
    showToast('Respuesta inválida del servidor.', 'error');
    return;
  }

  // Casos de respuesta
  if (xhr.status === 200 && data.success) {
    showToast(data.message, 'success');
    setTimeout(() => location.reload(), 1000);
  }
  else if (xhr.status === 422) {
      console.log(xhr.status)
    showToast(data.message, 'warning');
  }
  else {
    showToast(data.message || 'Error durante la carga.', 'error');
  }
};

  // error de red
  xhr.onerror = function() {
    overlay.style.display = 'none';
    showToast('No se pudo conectar con el servidor.', 'error');
  };

  xhr.send(formData);
});

// eliminr archivo
async function deleteFile(id) {
  if (!confirm('¿Seguro que deseas eliminar este archivo?')) return;
  try {
    const response = await fetch(`/files/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    });
    const data = await response.json();
    if (response.ok) {
      document.getElementById(`file-row-${id}`).remove();
      showToast('Archivo eliminado correctamente.', 'success');
    } else {
      showToast(data.message || 'No se pudo eliminar el archivo.', 'error');
    }
  } catch (err) {
    showToast('Error al eliminar el archivo.', 'error');
  }
}
</script>

<style>
    #uploadOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        z-index: 2000;
        color: #fff;
    }

    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
</style>
@endsection