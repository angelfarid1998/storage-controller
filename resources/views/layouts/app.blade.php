<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name', 'Storage Controller') }}</title>

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  {{-- chart.js --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  {{-- CSRF Token --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ url('/') }}">Storage Controller</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          @auth
          @if(optional(auth()->user()->role)->name === 'admin')
          <li class="nav-item"><a href="{{ route('settings.index') }}" class="nav-link">Configuración</a></li>
          <li class="nav-item"><a href="{{ route('groups.index') }}" class="nav-link">Grupos</a></li>
          <li class="nav-item"><a href="{{ route('users.index') }}" class="nav-link">Asignación</a></li>
          @endif
          <li class="nav-item"><a href="{{ route('files.index') }}" class="nav-link">Mis archivos</a></li>
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-link nav-link">Salir</button>
            </form>
          </li>
          @else
          <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">Iniciar sesión</a></li>
          <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">Registrarse</a></li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-4">
    @yield('content')
  </main>

  {{-- JS Bootstrap --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" style="z-index: 1100;"></div>
</body>

<script>
  document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.card').forEach(card => {
    card.style.opacity = 0;
    setTimeout(() => {
      card.style.transition = 'opacity 0.5s ease-in';
      card.style.opacity = 1;
    }, 200);
  });
});

function showToast(message, type = 'info') {
  const colors = {
    success: 'bg-success text-white',
    error: 'bg-danger text-white',
    warning: 'bg-warning text-dark',
    info: 'bg-primary text-white'
  };

  const toast = document.createElement('div');
  toast.className = `toast align-items-center ${colors[type] || colors.info}`;
  toast.role = 'alert';
  toast.ariaLive = 'assertive';
  toast.ariaAtomic = 'true';
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
  `;

  document.getElementById('toastContainer').appendChild(toast);
  const bsToast = new bootstrap.Toast(toast, { delay: 3500 });
  bsToast.show();

  toast.addEventListener('hidden.bs.toast', () => toast.remove());
}
</script>

</html>