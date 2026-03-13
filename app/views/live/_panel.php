<?php
$feedEndpoint = $isAdminView ? '/admin/live/feed' : '/live/feed';
?>
<div class="live-board" data-live-board data-endpoint="<?= e($feedEndpoint) ?>">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Centro de Live Scoring</h2>
    <span class="badge text-bg-dark" data-live-updated>Actualizando…</span>
  </div>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card p-3 h-100">
        <h5>Partidos y sets en vivo</h5>
        <div class="table-responsive">
          <table class="table table-modern align-middle" data-live-matches>
            <thead><tr><th>Partido</th><th>Mesa</th><th>Estado</th><th>Sets</th><th>Ganador</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card p-3 h-100">
        <h5>Llaves activas</h5>
        <div class="table-responsive">
          <table class="table table-modern" data-live-brackets>
            <thead><tr><th>Llave</th><th>Ronda</th><th>Match</th><th>Estado</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card p-3">
        <h5>Tablas de grupo actualizadas</h5>
        <div class="table-responsive">
          <table class="table table-modern" data-live-standings>
            <thead><tr><th>Torneo</th><th>Grupo</th><th>Pos</th><th>Jugador</th><th>PG</th><th>G/P</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
window.__LIVE_BOOTSTRAP__ = <?= json_encode($feed, JSON_UNESCAPED_UNICODE) ?>;
</script>
