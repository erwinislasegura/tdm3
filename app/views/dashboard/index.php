<h2 class="mb-4">Dashboard ejecutivo</h2>
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="metric card p-3"><small>Usuarios</small><h3><?= e((string)$metrics['users']) ?></h3></div></div>
  <div class="col-md-3"><div class="metric card p-3"><small>Jugadores</small><h3><?= e((string)$metrics['players']) ?></h3></div></div>
  <div class="col-md-3"><div class="metric card p-3"><small>Torneos</small><h3><?= e((string)$metrics['tournaments']) ?></h3></div></div>
  <div class="col-md-3"><div class="metric card p-3"><small>Torneos activos</small><h3><?= e((string)$metrics['active_tournaments']) ?></h3></div></div>
  <div class="col-md-3"><div class="metric card p-3"><small>Partidos pendientes</small><h3><?= e((string)$metrics['pending_matches']) ?></h3></div></div>
  <div class="col-md-3"><div class="metric card p-3"><small>Partidos finalizados</small><h3><?= e((string)$metrics['finished_matches']) ?></h3></div></div>
  <div class="col-md-3"><div class="metric card p-3"><small>Rankings activos</small><h3><?= e((string)$metrics['active_rankings']) ?></h3></div></div>
</div>
<div class="row g-3">
  <div class="col-lg-8"><div class="card p-3"><canvas id="dashboardChart" height="100"></canvas></div></div>
  <div class="col-lg-4"><div class="card p-3"><h6>Actividad reciente</h6><?php foreach($activities as $a): ?><div class="small border-bottom py-2"><b><?= e($a['action']) ?></b> · <?= e($a['module_name']) ?><br><span class="text-muted"><?= e($a['description']) ?></span></div><?php endforeach; ?></div></div>
</div>
