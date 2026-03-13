<h2>Organizaciones</h2>
<div class="card p-3 mb-3">
<form method="get" class="row g-2 mb-3"><div class="col-md-4"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar por nombre o ciudad"></div><div class="col-md-2"><button class="btn btn-outline-primary">Buscar</button></div></form>
<form method="post" action="/admin/organizations" class="row g-2"><?= csrf_field(); ?>
<div class="col-md-3"><input class="form-control" name="name" placeholder="Nombre" required></div>
<div class="col-md-2"><input class="form-control" name="type" placeholder="Tipo" required></div>
<div class="col-md-2"><input class="form-control" name="city" placeholder="Ciudad"></div>
<div class="col-md-2"><input class="form-control" name="email" placeholder="Email"></div>
<div class="col-md-2"><input class="form-control" name="phone" placeholder="Teléfono"></div>
<input type="hidden" name="description" value=""><input type="hidden" name="primary_color" value="#0f172a"><input type="hidden" name="secondary_color" value="#22d3ee"><input type="hidden" name="status" value="active">
<div class="col-md-1"><button class="btn btn-accent w-100">Crear</button></div></form>
</div>
<div class="card p-3 table-responsive"><table class="table table-modern"><thead><tr><th>Nombre</th><th>Tipo</th><th>Ciudad</th><th>Estado</th></tr></thead><tbody><?php foreach ($organizations as $o): ?><tr><td><?= e($o['name']) ?></td><td><?= e($o['type']) ?></td><td><?= e($o['city']) ?></td><td><span class="badge rounded-pill text-bg-success"><?= e($o['status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
<?php $pages = (int)ceil(($meta['total'] ?: 1) / $meta['per_page']); if($pages>1): ?><nav class="mt-3"><ul class="pagination"><?php for($i=1;$i<=$pages;$i++): ?><li class="page-item <?= $i===$meta['page']?'active':'' ?>"><a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
