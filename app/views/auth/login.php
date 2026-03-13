<div class="login-wrap">
  <div class="login-card card p-4 shadow-lg">
    <h3 class="fw-bold mb-1">Bienvenido a TDM Pro</h3>
    <p class="text-muted small mb-3">Acceso seguro para gestión profesional de torneos.</p>
    <form method="post" action="/login">
      <?= csrf_field(); ?>
      <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Contraseña</label><input type="password" name="password" class="form-control" required></div>
      <button class="btn btn-accent w-100">Ingresar</button>
    </form>
    <div class="small text-muted mt-3">
      ROOT demo: <b>root@system.local</b> / <b>Root12345!</b>
    </div>
  </div>
</div>
