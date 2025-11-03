@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>📚 Asignación de Grupos a Usuarios</h2>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">⬅ Volver</a>
  </div>

  <table class="table table-bordered align-middle shadow-sm">
    <thead class="table-light text-center">
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Rol</th>
        <th>Grupo</th>
        <th>Cuota personal (bytes)</th>
        <th>Uso / Cuota</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $user)
      <tr data-id="{{ $user->id }}">
        <td>{{ $loop->iteration }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ ucfirst($user->role->name) }}</td>
        <td>
          <select class="form-select form-select-sm groupSelect">
            <option value="">— Sin grupo —</option>
            @foreach($groups as $group)
            <option value="{{ $group->id }}" {{ $user->group_id == $group->id ? 'selected' : '' }}>
              {{ $group->name }}
            </option>
            @endforeach
          </select>
        </td>
        {{-- definir cuota para cada usuario --}}
        <td>
          <input type="number" class="form-control form-control-sm quotaInput" value="{{ $user->quota ?? '' }}"
            min="1024" placeholder="Ej: 10485760">
        </td>
        {{-- Mostrar uso de almacenamiento --}}
        @php
        $percent = ($user->used_space / $user->quota_limit) * 100;
        $color = $percent >= 90 ? 'text-danger fw-bold' : ($percent >= 70 ? 'text-warning fw-semibold' :
        'text-success');
        @endphp

        <td class="text-center {{ $color }}">
          {{ number_format($user->used_space / 1024 / 1024, 2) }} MB /
          {{ number_format($user->quota_limit / 1024 / 1024, 2) }} MB
        </td>
        <td class="text-center">
          <button class="btn btn-sm btn-outline-success assignBtn">
            <i class="bi bi-check2-circle"></i> Guardar
          </button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
  document.querySelectorAll('.assignBtn').forEach(btn => {
  btn.addEventListener('click', async (e) => {
    const tr = e.target.closest('tr');
    const userId = tr.dataset.id;
    const groupSelect = tr.querySelector('.groupSelect');
    const quotaInput = tr.querySelector('.quotaInput');

    const groupId = groupSelect ? (groupSelect.value || null) : null;
    const quota = quotaInput ? (quotaInput.value || null) : null;

    const url = `/admin/users/${userId}`;

    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          _method: 'PUT',
          group_id: groupId,
          quota: quota
        })
      });

      let data = {};
      try { data = await res.json(); } catch (err) {}

      if (res.ok && data.success) {
        showToast(data.message || 'Usuario actualizado correctamente.', 'success');
        // refrescar la fila o la página
        setTimeout(() => location.reload(), 800);
      } else if (res.status === 422) {
        // Validación de espacio
        const msg = data.message || 'Datos inválidos. Verifica la cuota y el grupo.';
        showToast(msg, 'warning');
      } else {
        showToast(data.message || 'Error al actualizar usuario.', 'error');
        console.error('Update user error', res.status, data);
      }
    } catch (err) {
      showToast('Error de red al actualizar usuario.', 'error');
      console.error(err);
    }
  });
});
</script>

@endsection