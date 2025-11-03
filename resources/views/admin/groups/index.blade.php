@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>🆎 Grupos</h2>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">⬅ Volver</a>
  </div>

  <form id="groupForm" class="row g-3 align-items-center mb-4">
    <input type="hidden" id="groupId">
    <div class="col-md-5">
      <input type="text" id="groupName" class="form-control" placeholder="Nombre del grupo" required>
    </div>
    <div class="col-md-3">
      <input type="number" id="groupQuota" class="form-control" placeholder="Cuota (bytes)" required min="1024">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-success w-100">Guardar</button>
    </div>
    <div class="col-md-2">
      <button type="button" id="cancelEdit" class="btn btn-secondary w-100" style="display:none;">Cancelar</button>
    </div>
  </form>

  <table class="table table-bordered align-middle shadow-sm">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Cuota (bytes)</th>
        <th width="150">Acciones</th>
      </tr>
    </thead>
    <tbody id="groupTable">
      @foreach($groups as $group)
      <tr data-id="{{ $group->id }}" data-name="{{ $group->name }}" data-quota="{{ $group->quota }}">
        <td>{{ $loop->iteration }}</td>
        <td>{{ $group->name }}</td>
        <td>{{ number_format($group->quota) }}</td>
        <td>
          <div class="d-flex justify-content-center gap-2">
            <button class="btn btn-sm btn-outline-primary editBtn d-flex align-items-center gap-1">
              <i class="bi bi-pencil-square"></i> Editar
            </button>
            <button class="btn btn-sm btn-outline-danger deleteBtn d-flex align-items-center gap-1">
              <i class="bi bi-trash3"></i> Eliminar
            </button>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
  const form = document.getElementById('groupForm');
const nameInput = document.getElementById('groupName');
const quotaInput = document.getElementById('groupQuota');
const idField = document.getElementById('groupId');
const cancelBtn = document.getElementById('cancelEdit');

// crear o editar grupo
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const name = nameInput.value.trim();
  const quota = quotaInput.value.trim();
  if (!name || !quota) return showToast('Completa todos los campos.', 'warning');

  const id = idField.value;
  const method = id ? 'PUT' : 'POST';
  const url = id ? `/admin/groups/${id}` : '/admin/groups';

  const res = await fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ name, quota })
  });

  const data = await res.json();
  if (res.status === 200 && data.success) {
    showToast(data.message, 'success');
    setTimeout(() => location.reload(), 1000);
  } 
  else if (res.status === 422 && data.message) {
    const msg = 'Ya existe un grupo con ese nombre.';
    showToast(msg, 'warning');
  } 
  else {
    showToast('Error al guardar el grupo.', 'error');
  }
});

// cargar datos para editar
document.querySelectorAll('.editBtn').forEach(btn => {
  btn.addEventListener('click', e => {
    const tr = e.target.closest('tr');
    idField.value = tr.dataset.id;
    nameInput.value = tr.dataset.name;
    quotaInput.value = tr.dataset.quota;
    cancelBtn.style.display = 'inline-block';
    form.querySelector('button[type="submit"]').textContent = 'Actualizar';
  });
});

// Cancelar edición
cancelBtn.addEventListener('click', () => {
  idField.value = '';
  nameInput.value = '';
  quotaInput.value = '';
  cancelBtn.style.display = 'none';
  form.querySelector('button[type="submit"]').textContent = 'Guardar';
});

// Eliminar grupo
document.querySelectorAll('.deleteBtn').forEach(btn => {
  btn.addEventListener('click', async e => {
    const tr = e.target.closest('tr');
    if (!confirm(`¿Eliminar el grupo "${tr.dataset.name}"?`)) return;

    const res = await fetch(`/admin/groups/${tr.dataset.id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    const data = await res.json();
    if (res.status === 200 && data.success) {
      showToast(data.message, 'success');
      setTimeout(() => location.reload(), 1000);
    } else {
      showToast('Error al eliminar el grupo.', 'error');
    }
  });
});
</script>
<style>
  .table th,
  .table td {
    vertical-align: middle;
  }

  .btn-sm {
    padding: 4px 8px;
    font-size: 0.875rem;
  }
</style>
@endsection