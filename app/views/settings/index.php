<h2>Configuración global (ROOT)</h2>
<div class="card p-3">
<form method="post" action="/admin/settings" class="row g-3"><?= csrf_field(); ?>
<div class="col-md-4"><label class="form-label">Nombre plataforma</label><input name="platform_name" class="form-control" value="<?= e($settings['platform_name'] ?? '') ?>"></div>
<div class="col-md-4"><label class="form-label">Email principal</label><input name="contact_email" class="form-control" value="<?= e($settings['contact_email'] ?? '') ?>"></div>
<div class="col-md-4"><label class="form-label">Teléfono</label><input name="contact_phone" class="form-control" value="<?= e($settings['contact_phone'] ?? '') ?>"></div>
<div class="col-md-6"><label class="form-label">Dirección</label><input name="address" class="form-control" value="<?= e($settings['address'] ?? '') ?>"></div>
<div class="col-md-3"><label class="form-label">Timezone</label><input name="timezone" class="form-control" value="<?= e($settings['timezone'] ?? 'America/Santiago') ?>"></div>
<div class="col-md-3"><label class="form-label">Mantenimiento</label><select name="maintenance_mode" class="form-select"><option value="0" <?= (($settings['maintenance_mode'] ?? '0')==='0')?'selected':'' ?>>No</option><option value="1" <?= (($settings['maintenance_mode'] ?? '0')==='1')?'selected':'' ?>>Sí</option></select></div>
<div class="col-md-6"><label class="form-label">Color principal</label><input name="primary_color" class="form-control" value="<?= e($settings['primary_color'] ?? '#0f172a') ?>"></div>
<div class="col-md-6"><label class="form-label">Color acento</label><input name="accent_color" class="form-control" value="<?= e($settings['accent_color'] ?? '#06b6d4') ?>"></div>
<div class="col-12"><button class="btn btn-accent">Guardar configuración</button></div>
</form></div>
