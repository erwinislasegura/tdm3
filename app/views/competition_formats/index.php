<h2>Formato de Competencia (Grupos + Knockout)</h2>
<div class="card p-3 mb-3">
  <form method="get" class="row g-2 mb-3">
    <div class="col-md-4"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Buscar categoría o torneo"></div>
    <div class="col-md-2"><button class="btn btn-outline-primary">Buscar</button></div>
  </form>
  <form method="post" action="/admin/competition-formats" class="row g-2"><?= csrf_field(); ?>
    <div class="col-md-2"><select name="tournament_id" class="form-select" required><?php foreach($tournaments as $t): ?><option value="<?= e((string)$t['id']) ?>"><?= e($t['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><input name="category_name" class="form-control" placeholder="Categoría" required></div>
    <div class="col-md-1"><input name="registered_players" class="form-control" type="number" min="2" value="16" required></div>
    <div class="col-md-1"><input name="group_count" class="form-control" type="number" min="1" value="4" required></div>
    <div class="col-md-1"><input name="group_size" class="form-control" type="number" min="2" value="4" required></div>
    <div class="col-md-1"><input name="qualified_per_group" class="form-control" type="number" min="1" value="2" required></div>
    <div class="col-md-2"><select name="advancement_mode" class="form-select"><option value="group_winners">Solo ganadores</option><option value="top2">Top 2</option><option value="best_thirds">Mejores terceros</option></select></div>
    <div class="col-md-1"><input name="best_third_slots" class="form-control" type="number" min="0" value="0"></div>
    <div class="col-md-2"><select name="ranking_criteria" class="form-select"><option value="system_ranking">Ranking sistema</option><option value="tournament_ranking">Ranking torneo</option><option value="manual_ranking">Manual</option></select></div>
    <div class="col-md-2"><select name="separation_rule" class="form-select"><option value="club">Club</option><option value="association">Asociación</option><option value="federation">Federación</option><option value="none">Sin separación</option></select></div>
    <div class="col-md-2"><input name="protected_seeds" class="form-control" type="number" min="2" value="8"></div>
    <div class="col-md-2"><select name="generation_mode" class="form-select"><option value="automatic">Automática</option><option value="assisted_manual">Manual asistida</option></select></div>
    <input type="hidden" name="allow_same_group_early_cross" value="0">
    <input type="hidden" name="best_third_criteria" value="match_points">
    <div class="col-md-12"><button class="btn btn-accent">Crear configuración</button></div>
  </form>
</div>

<div class="card p-3 table-responsive">
  <table class="table table-modern"><thead><tr><th>ID</th><th>Torneo</th><th>Categoría</th><th>Grupos</th><th>Clas.</th><th>Seeds</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
  <?php foreach($formats as $f): ?>
    <tr>
      <td><?= e((string)$f['id']) ?></td><td><?= e($f['tournament_name']) ?></td><td><?= e($f['category_name']) ?></td><td><?= e((string)$f['group_count']) ?> x <?= e((string)$f['group_size']) ?></td>
      <td><?= e((string)$f['qualified_per_group']) ?></td><td><?= e((string)$f['protected_seeds']) ?></td><td><span class="badge rounded-pill text-bg-secondary"><?= e($f['status']) ?></span></td>
      <td><a class="btn btn-sm btn-outline-primary" href="/admin/competition-formats/<?= e((string)$f['id']) ?>">Ver</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody></table>
</div>
