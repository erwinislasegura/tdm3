<div class="login-wrap">
  <div class="login-card card p-4 shadow-lg border-0">
    <div class="text-center mb-3">
      <div class="login-ball mx-auto mb-2"><i class="fa-solid fa-table-tennis-paddle-ball"></i></div>
      <h3 class="fw-bold mb-1">TDM Pro Manager</h3>
      <p class="text-muted small mb-0">Acceso deportivo profesional para gestión de torneos.</p>
    </div>

    <form method="post" action="/login" class="mt-2">
      <?= csrf_field(); ?>
      <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control form-control-lg" placeholder="tu@email.com" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Contraseña</label>
        <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
      </div>
      <button class="btn btn-accent w-100 btn-lg"><i class="fa-solid fa-right-to-bracket me-1"></i> Ingresar</button>
    </form>

    <div class="small text-muted mt-3 text-center">
      ROOT demo: <b>root@system.local</b> / <b>Root12345!</b>
    </div>
  </div>
</div>
