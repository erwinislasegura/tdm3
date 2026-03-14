<?php use App\Services\PermissionService; ?>

<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small mb-1">
    <li class="breadcrumb-item">Torneos</li>
    <li class="breadcrumb-item">Torneo Múltiple</li>
    <li class="breadcrumb-item"><?= e($format['tournament_name']) ?></li>
    <li class="breadcrumb-item">Fases</li>
    <li class="breadcrumb-item active">Grupos</li>
  </ol>
</nav>

<div class="card p-3 mb-3">
  <div class="d-flex justify-content-between flex-wrap gap-3 align-items-start">
    <div>
      <h3 class="mb-1"><?= e($format['tournament_name']) ?></h3>
      <div class="text-secondary">Categoría: <strong><?= e($format['category_name']) ?></strong> · Fase: <strong>Grupos</strong></div>
      <span class="badge text-bg-info mt-2">Estado: <?= e($format['status']) ?></span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-outline-secondary btn-sm" href="/admin/tournaments">Volver a Torneo</a>
      <a class="btn btn-outline-secondary btn-sm" href="/admin/competition-formats">Volver a Fases</a>
      <?php if (PermissionService::can('groups.generate')): ?>
      <form method="post" action="/admin/competition-formats/<?= (int)$format['id'] ?>/generate-groups"><?= csrf_field(); ?><button class="btn btn-primary btn-sm">Generar / Regenerar grupos</button></form>
      <?php endif; ?>
      <?php if (PermissionService::can('qualifications.generate')): ?>
      <form method="post" action="/admin/competition-formats/<?= (int)$format['id'] ?>/close-groups"><?= csrf_field(); ?><button class="btn btn-warning btn-sm">Cerrar grupos y clasificar</button></form>
      <?php endif; ?>
    </div>
  </div>
</div>

<ul class="nav nav-tabs mb-3">
  <?php foreach (['Juegos','Participantes','Calendario','Grupos','Llaves','Resultados','En Vivo'] as $tab): ?>
    <li class="nav-item"><span class="nav-link <?= $tab === 'Grupos' ? 'active' : '' ?>"><?= $tab ?></span></li>
  <?php endforeach; ?>
</ul>

<div class="card p-3 mb-3">
  <h6 class="mb-3">Necesarios para empezar a jugar</h6>
  <div class="row g-2">
    <?php
      $labels = ['participants' => 'Participantes inscritos', 'draw' => 'Sorteo realizado', 'matches' => 'Partidos generados', 'phase_enabled' => 'Fase habilitada'];
      foreach ($labels as $key => $label):
      $ok = (bool)($checklist[$key] ?? false);
    ?>
      <div class="col-md-3 col-6"><span class="badge <?= $ok ? 'text-bg-success' : 'text-bg-secondary' ?> w-100 p-2 text-start"><?= $ok ? '✔' : '○' ?> <?= $label ?></span></div>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($groups === []): ?>
  <div class="card p-4 text-center">
    <h5 class="mb-2">Aún no hay grupos para esta fase</h5>
    <?php foreach ($emptyStates as $msg): ?><p class="text-secondary mb-1"><?= e($msg) ?></p><?php endforeach; ?>
    <?php if (PermissionService::can('groups.generate')): ?>
      <form method="post" action="/admin/competition-formats/<?= (int)$format['id'] ?>/generate-groups" class="mt-3"><?= csrf_field(); ?>
        <button class="btn btn-primary">Generar grupos</button>
      </form>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php foreach($groups as $group): $detail = $groupDetails[$group['id']] ?? ['players'=>[], 'matches'=>[], 'standings'=>[]]; ?>
<div class="card p-3 mb-3">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
    <h4 class="mb-0"><?= e($group['name']) ?></h4>
    <div class="d-flex gap-2 flex-wrap align-items-center">
      <span class="badge text-bg-light">Jugadores: <?= (int)$group['players_count'] ?></span>
      <span class="badge text-bg-light">Clasificados: <?= (int)$group['qualified_count'] ?></span>
      <span class="badge <?= ((int)$group['is_locked']===1) ? 'text-bg-dark' : 'text-bg-success' ?>"><?= ((int)$group['is_locked']===1) ? 'Bloqueado' : 'Activo' ?></span>
      <?php if (PermissionService::can('standings.recalculate')): ?>
      <form method="post" action="/admin/competition-formats/<?= (int)$format['id'] ?>/groups/<?= (int)$group['id'] ?>/recalculate"><?= csrf_field(); ?><button class="btn btn-outline-primary btn-sm">Recalcular tabla</button></form>
      <?php endif; ?>
      <?php if (PermissionService::can('groups.lock')): ?>
      <form method="post" action="/admin/competition-formats/<?= (int)$format['id'] ?>/groups/<?= (int)$group['id'] ?>/lock"><?= csrf_field(); ?><button class="btn btn-outline-dark btn-sm">Bloquear grupo</button></form>
      <?php endif; ?>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-4">
      <div class="border rounded p-2 h-100">
        <h6>Integrantes</h6>
        <table class="table table-sm table-modern mb-0">
          <thead><tr><th>Seed</th><th>Jugador</th><th>Club</th><th>Pos</th></tr></thead>
          <tbody>
          <?php foreach($detail['players'] as $p): ?>
            <tr><td><?= (int)$p['seed_number'] ?></td><td><?= e($p['player_name']) ?></td><td><?= e($p['club_name']) ?></td><td><?= e((string)($p['current_position'] ?? '-')) ?></td></tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="border rounded p-2 h-100">
        <h6>Tabla de posiciones</h6>
        <table class="table table-sm table-modern mb-0">
          <thead><tr><th>Pos</th><th>Jugador</th><th>PJ</th><th>PG</th><th>PP</th><th>MP</th><th>Sets +</th><th>Sets -</th><th>Ratio Sets</th><th>Puntos +</th><th>Puntos -</th><th>Ratio Puntos</th><th>Estado</th></tr></thead>
          <tbody>
          <?php foreach($detail['standings'] as $s): ?>
            <tr>
              <td><?= (int)$s['position'] ?></td><td><?= e($s['player_name']) ?></td><td><?= (int)$s['played'] ?></td><td><?= (int)$s['won'] ?></td><td><?= (int)$s['lost'] ?></td>
              <td><?= (int)$s['match_points'] ?></td><td><?= (int)($s['sets_for'] ?? $s['games_for']) ?></td><td><?= (int)($s['sets_against'] ?? $s['games_against']) ?></td>
              <td><?= number_format((float)($s['sets_ratio'] ?? $s['game_ratio']), 2) ?></td><td><?= (int)$s['points_for'] ?></td><td><?= (int)$s['points_against'] ?></td>
              <td><?= number_format((float)$s['point_ratio'], 2) ?></td><td><span class="badge <?= ((int)($s['qualified'] ?? 0)===1) ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= ((int)($s['qualified'] ?? 0)===1) ? 'Clasificado' : 'Pendiente' ?></span></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-3 border rounded p-2">
    <h6>Partidos del grupo</h6>
    <table class="table table-sm table-modern mb-0">
      <thead><tr><th>Partido</th><th>Mesa</th><th>Fecha/Hora</th><th>Estado</th><th>Marcador</th><th>Sets</th><th>Ganador</th><th>Acciones</th></tr></thead>
      <tbody>
      <?php foreach($detail['matches'] as $m): ?>
        <tr>
          <td><?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?></td>
          <td><?= e((string)($m['table_number'] ?? '-')) ?></td>
          <td><?= e((string)($m['scheduled_at'] ?? '-')) ?></td>
          <td><span class="badge text-bg-light"><?= e($m['status']) ?></span></td>
          <td><?= (int)($m['sets_won_a'] ?? 0) ?> - <?= (int)($m['sets_won_b'] ?? 0) ?></td>
          <td><code><?= e((string)($m['sets_json'] ?? '[]')) ?></code></td>
          <td><?= (int)($m['winner_player_id'] ?? 0) === (int)$m['player_a_id'] ? e($m['player_a_name']) : ((int)($m['winner_player_id'] ?? 0) === (int)$m['player_b_id'] ? e($m['player_b_name']) : '-') ?></td>
          <td>
            <?php if (PermissionService::can('matches.score')): ?>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scoreModal<?= (int)$m['id'] ?>">Registrar score</button>
            <?php else: ?><span class="text-secondary small">Solo lectura</span><?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php foreach($detail['matches'] as $m): ?>
<div class="modal fade" id="scoreModal<?= (int)$m['id'] ?>" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Score: <?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="post" action="/admin/competition-formats/<?= (int)$format['id'] ?>/matches/<?= (int)$m['id'] ?>/score">
      <div class="modal-body">
        <?= csrf_field(); ?>
        <div class="row g-2 mb-2"><div class="col-md-4"><label class="form-label">Estado</label><select name="status" class="form-select form-select-sm"><?php foreach(['pending','scheduled','called','in_game','finished','suspended','walkover'] as $status): ?><option value="<?= $status ?>" <?= $m['status']===$status?'selected':'' ?>><?= $status ?></option><?php endforeach; ?></select></div><div class="col-md-4"><label class="form-label">Mesa</label><input class="form-control form-control-sm" name="table_number" value="<?= e((string)($m['table_number'] ?? '')) ?>"></div><div class="col-md-4"><label class="form-label">Fecha/Hora</label><input class="form-control form-control-sm" type="datetime-local" name="scheduled_at"></div></div>
        <label class="form-label">Sets JSON</label>
        <textarea name="sets_json" class="form-control form-control-sm" rows="3" placeholder='[{"a":11,"b":8},{"a":8,"b":11},{"a":11,"b":5}]'><?= e((string)($m['sets_json'] ?: '[]')) ?></textarea>
        <label class="form-label mt-2">ID ganador (opcional)</label>
        <input name="winner_player_id" class="form-control form-control-sm" placeholder="<?= (int)$m['player_a_id'] ?> o <?= (int)$m['player_b_id'] ?>">
      </div>
      <div class="modal-footer"><button class="btn btn-primary btn-sm">Guardar score</button></div>
    </form>
  </div></div>
</div>
<?php endforeach; ?>
<?php endforeach; ?>

<div class="card p-3">
  <h4>Llaves (knockout)</h4>
  <?php if($bracket): ?>
    <p>Draw size: <?= e((string)$bracket['draw_size']) ?> | Estado: <?= e($bracket['status']) ?></p>
    <table class="table table-sm table-modern"><thead><tr><th>Slot</th><th>Jugador</th><th>Seed</th><th>Origen</th></tr></thead><tbody>
      <?php foreach($slots as $s): ?><tr><td><?= e((string)$s['slot_number']) ?></td><td><?= e($s['player_name'] ?: 'BYE') ?></td><td><?= e((string)$s['seed_number']) ?></td><td><?= e((string)$s['source_ref']) ?></td></tr><?php endforeach; ?>
    </tbody></table>
  <?php else: ?><p class="text-secondary">Aún no hay cuadro generado.</p><?php endif; ?>
  <?php if (PermissionService::can('knockout.generate')): ?>
    <form method="post" action="/admin/competition-formats/<?= (int)$format['id'] ?>/generate-knockout"><?= csrf_field(); ?><button class="btn btn-success btn-sm">Generar llaves</button></form>
  <?php endif; ?>
</div>
