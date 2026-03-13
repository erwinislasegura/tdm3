<h2 class="mb-3">Torneos</h2>
<div class="row g-3"><?php foreach ($tournaments as $t): ?><div class="col-md-6"><div class="card p-3 h-100"><h5><?= e($t['name']) ?></h5><p><?= e($t['description']) ?></p><small><?= e($t['city']) ?> · <?= e($t['start_date']) ?></small></div></div><?php endforeach; ?></div>
