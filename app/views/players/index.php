<h2>Jugadores</h2>
<div class="card p-3 mb-3">
<form method="get" class="row g-2 mb-3"><div class="col-md-4"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar jugador/club"></div><div class="col-md-2"><button class="btn btn-outline-primary">Buscar</button></div></form>
<form method="post" action="/admin/players" class="row g-2"><?= csrf_field(); ?>
<div class="col-md-2"><input name="first_name" class="form-control" placeholder="Nombres" required></div>
<div class="col-md-2"><input name="last_name" class="form-control" placeholder="Apellidos" required></div>
<div class="col-md-2"><input name="document" class="form-control" placeholder="Documento"></div>
<div class="col-md-2"><input type="date" name="birth_date" class="form-control"></div>
<div class="col-md-1"><select name="gender" class="form-select"><option value="M">M</option><option value="F">F</option></select></div>
<div class="col-md-1"><input name="nationality" class="form-control" placeholder="Nac."></div>
<div class="col-md-2"><select name="club_id" class="form-select"><option value="">Club</option><?php foreach($clubs as $c): ?><option value="<?= e((string)$c['id']) ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
<input type="hidden" name="current_category" value="Todo Competidor"><input type="hidden" name="notes" value="">
<div class="col-md-12"><button class="btn btn-accent">Guardar jugador</button></div></form></div>
<div class="card p-3 table-responsive"><table class="table table-modern"><thead><tr><th>Jugador</th><th>Club</th><th>Género</th><th>Puntos</th><th>Estado</th></tr></thead><tbody><?php foreach($players as $p): ?><tr><td><?= e($p['first_name'].' '.$p['last_name']) ?></td><td><?= e($p['club_name']) ?></td><td><?= e($p['gender']) ?></td><td><?= e((string)$p['ranking_points']) ?></td><td><span class="badge rounded-pill text-bg-primary"><?= e($p['status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
<?php $pages = (int)ceil(($meta['total'] ?: 1) / $meta['per_page']); if($pages>1): ?><nav class="mt-3"><ul class="pagination"><?php for($i=1;$i<=$pages;$i++): ?><li class="page-item <?= $i===$meta['page']?'active':'' ?>"><a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a></li><?php endfor; ?></ul></nav><?php endif; ?>
