<h2>Formato #<?= e((string)$format['id']) ?> - <?= e($format['category_name']) ?></h2>
<div class="d-flex gap-2 mb-3">
  <form method="post" action="/admin/competition-formats/<?= e((string)$format['id']) ?>/generate-groups"><?= csrf_field(); ?><button class="btn btn-primary">Generar grupos</button></form>
  <form method="post" action="/admin/competition-formats/<?= e((string)$format['id']) ?>/close-groups"><?= csrf_field(); ?><button class="btn btn-warning">Cerrar grupos y clasificar</button></form>
  <form method="post" action="/admin/competition-formats/<?= e((string)$format['id']) ?>/generate-knockout"><?= csrf_field(); ?><button class="btn btn-success">Generar knockout</button></form>
</div>

<?php foreach($groups as $group): $detail = $groupDetails[$group['id']] ?? ['players'=>[], 'matches'=>[], 'standings'=>[]]; ?>
<div class="card p-3 mb-3">
  <h4><?= e($group['name']) ?></h4>
  <div class="row">
    <div class="col-md-4"><strong>Jugadores</strong><ul><?php foreach($detail['players'] as $p): ?><li><?= e($p['player_name']) ?> (S<?= e((string)$p['seed_number']) ?>)</li><?php endforeach; ?></ul></div>
    <div class="col-md-8"><strong>Partidos</strong>
      <table class="table table-sm"><thead><tr><th>Encuentro</th><th>Estado</th><th>Ganador</th><th>Acción</th></tr></thead><tbody>
      <?php foreach($detail['matches'] as $m): ?>
        <tr>
          <td><?= e($m['player_a_name']) ?> vs <?= e($m['player_b_name']) ?></td>
          <td><?= e($m['status']) ?></td>
          <td><?= e((string)$m['winner_player_id']) ?></td>
          <td>
            <form method="post" action="/admin/competition-formats/<?= e((string)$format['id']) ?>/matches/<?= e((string)$m['id']) ?>/score" class="d-flex gap-1"><?= csrf_field(); ?>
              <input name="winner_player_id" placeholder="ID ganador" class="form-control form-control-sm" style="width:110px" required>
              <input name="sets_json" placeholder='[{"a":11,"b":8}]' class="form-control form-control-sm" style="width:180px">
              <input type="hidden" name="status" value="finished">
              <button class="btn btn-sm btn-outline-primary">Guardar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
  </div>

  <strong>Tabla en vivo</strong>
  <table class="table table-sm"><thead><tr><th>Pos</th><th>Jugador</th><th>PJ</th><th>PG</th><th>PP</th><th>MP</th><th>GF</th><th>GC</th><th>PF</th><th>PC</th></tr></thead><tbody>
  <?php foreach($detail['standings'] as $s): ?>
    <tr><td><?= e((string)$s['position']) ?></td><td><?= e($s['player_name']) ?></td><td><?= e((string)$s['played']) ?></td><td><?= e((string)$s['won']) ?></td><td><?= e((string)$s['lost']) ?></td><td><?= e((string)$s['match_points']) ?></td><td><?= e((string)$s['games_for']) ?></td><td><?= e((string)$s['games_against']) ?></td><td><?= e((string)$s['points_for']) ?></td><td><?= e((string)$s['points_against']) ?></td></tr>
  <?php endforeach; ?></tbody></table>
</div>
<?php endforeach; ?>

<div class="card p-3">
  <h4>Cuadro knockout</h4>
  <?php if($bracket): ?>
    <p>Draw size: <?= e((string)$bracket['draw_size']) ?> | Estado: <?= e($bracket['status']) ?></p>
    <table class="table table-sm"><thead><tr><th>Slot</th><th>Jugador</th><th>Seed</th><th>Origen</th></tr></thead><tbody>
      <?php foreach($slots as $s): ?><tr><td><?= e((string)$s['slot_number']) ?></td><td><?= e($s['player_name'] ?: 'BYE') ?></td><td><?= e((string)$s['seed_number']) ?></td><td><?= e((string)$s['source_ref']) ?></td></tr><?php endforeach; ?>
    </tbody></table>
  <?php else: ?><p>Aún no hay cuadro generado.</p><?php endif; ?>
</div>
