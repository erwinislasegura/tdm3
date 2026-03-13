<header class="topbar px-4 py-3 mb-3">
  <div>
    <h6 class="mb-0">Panel de control</h6>
    <small class="text-muted">Operación en tiempo real y gestión integral</small>
  </div>
  <div class="d-flex align-items-center gap-3">
    <span class="badge text-bg-light"><?= e(auth_user()['role_name'] ?? '') ?></span>
    <span><?= e(auth_user()['name'] ?? '') ?></span>
    <form method="post" action="/logout" class="mb-0"><?= csrf_field(); ?><button class="btn btn-outline-danger btn-sm">Salir</button></form>
  </div>
</header>
