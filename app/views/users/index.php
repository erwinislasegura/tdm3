<h2>Usuarios y roles</h2>
<div class="card p-3 mb-3">
<form method="get" class="row g-2 mb-3"><div class="col-md-4"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar usuario"></div><div class="col-md-2"><button class="btn btn-outline-primary">Buscar</button></div></form>
<form method="post" action="/admin/users" class="row g-2"><?= csrf_field(); ?>
<div class="col-md-3"><input name="name" class="form-control" placeholder="Nombre" required></div>
<div class="col-md-3"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
<div class="col-md-2"><input name="password" type="password" class="form-control" placeholder="Contraseña" required></div>
<div class="col-md-2"><select name="role_id" class="form-select" required><?php foreach($roles as $r): ?><option value="<?= e((string)$r['id']) ?>"><?= e($r['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><button class="btn btn-accent w-100">Crear usuario</button></div>
</form></div>
<div class="card p-3 table-responsive"><table class="table table-modern"><thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><span class="badge text-bg-info"><?= e($u['role_name']) ?></span></td><td><?= e($u['status']) ?></td></tr><?php endforeach; ?></tbody></table></div>
