<h2>Torneos</h2>
<div class="card p-3 mb-3">
<form method="get" class="row g-2 mb-3"><div class="col-md-4"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar torneo/organización"></div><div class="col-md-2"><button class="btn btn-outline-primary">Buscar</button></div></form>
<form method="post" action="/admin/tournaments" class="row g-2"><?= csrf_field(); ?>
<div class="col-md-2"><select name="organization_id" class="form-select" required><?php foreach($organizations as $o): ?><option value="<?= e((string)$o['id']) ?>"><?= e($o['name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><input name="name" class="form-control" placeholder="Torneo" required></div>
<div class="col-md-2"><input type="date" name="start_date" class="form-control" required></div>
<div class="col-md-2"><input type="date" name="end_date" class="form-control" required></div>
<div class="col-md-2"><input name="city" class="form-control" placeholder="Ciudad"></div>
<div class="col-md-2"><input name="venue" class="form-control" placeholder="Recinto"></div>
<input type="hidden" name="description" value=""><input type="hidden" name="status" value="draft"><input type="hidden" name="is_public" value="1">
<div class="col-md-12"><button class="btn btn-accent">Crear torneo</button></div>
</form></div>
<div class="card p-3 table-responsive"><table class="table table-modern"><thead><tr><th>Torneo</th><th>Organización</th><th>Fecha inicio</th><th>Estado</th></tr></thead><tbody><?php foreach($tournaments as $t): ?><tr><td><?= e($t['name']) ?></td><td><?= e($t['organization_name']) ?></td><td><?= e($t['start_date']) ?></td><td><span class="badge rounded-pill text-bg-secondary"><?= e($t['status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
<?php $pages = (int)ceil(($meta['total'] ?: 1) / $meta['per_page']); if($pages>1): ?><nav class="mt-3"><ul class="pagination"><?php for($i=1;$i<=$pages;$i++): ?><li class="page-item <?= $i===$meta['page']?'active':'' ?>"><a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
