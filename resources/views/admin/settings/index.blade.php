@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>⚙️ Configuración Global</h2>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">⬅ Volver</a>
    </div>

    <form id="settingsForm" class="row g-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Cuota global por defecto (bytes)</label>
            <input type="number" id="defaultQuota" class="form-control" value="{{ $defaultQuota }}" min="1024" required>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Extensiones prohibidas</label>
            <input type="text" id="forbiddenExtensions" class="form-control" value="{{ $forbiddenExt }}"
                placeholder="ej: exe,bat,js,php,sh" required>
        </div>

        <div class="col-md-2 mt-4 pt-4">
            <button type="submit" class="btn btn-outline-success w-100"> <i class="bi bi-download"> </i>Guardar</button>
        </div>
    </form>
</div>

<script>
    const form = document.getElementById('settingsForm');
form.addEventListener('submit', async e => {
    e.preventDefault();
    const default_quota = document.getElementById('defaultQuota').value.trim();
    const forbidden_extensions = document.getElementById('forbiddenExtensions').value.trim();

    const res = await fetch('{{ route('settings.update') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ default_quota, forbidden_extensions })
    });

    const data = await res.json();
    if (res.ok && data.success) {
        showToast(data.message, 'success');
    } else {
        showToast('Error al guardar la configuración.', 'error');
    }
});
</script>
@endsection